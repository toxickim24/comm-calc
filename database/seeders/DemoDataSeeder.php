<?php

namespace Database\Seeders;

use App\Enums\DealStatus;
use App\Models\AuditLog;
use App\Models\CommissionPayout;
use App\Models\CommissionSetting;
use App\Models\Deal;
use App\Models\SpiffPayout;
use App\Models\User;
use App\Models\WeeklyScore;
use App\Services\CommissionCalculatorService;
use App\Services\SpiffCalculatorService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $john = User::where('email', 'john@baysidepavers.com')->first();
        $jane = User::where('email', 'jane@baysidepavers.com')->first();

        if (!$john || !$jane) {
            $this->command->error('Run DatabaseSeeder first: php artisan db:seed');
            return;
        }

        $this->command->info('Seeding demo deals...');
        $this->seedDeals($john, $jane);

        $this->command->info('Calculating commissions...');
        $this->calculateCommissions();

        $this->command->info('Generating weekly scores...');
        $this->generateWeeklyScores();

        $this->command->info('Calculating SPIFFs...');
        $this->calculateSpiffs();

        $this->command->info('Seeding audit log entries...');
        $this->seedAuditLogs($john, $jane);

        $this->command->info('Demo data seeded successfully!');
    }

    protected function seedDeals(User $john, User $jane): void
    {
        $deals = [
            // === 3 MONTHS AGO — John: strong month, Jane: average ===
            ['user' => $john, 'months_ago' => 3, 'client' => 'Martinez Residence',      'value' => 32000, 'gm' => 48, 'status' => 'closed_won',  'appt_offset' => 1,  'sign_offset' => 3,  'deposit_offset' => 5],
            ['user' => $john, 'months_ago' => 3, 'client' => 'Oak Park HOA',            'value' => 55000, 'gm' => 44, 'status' => 'closed_won',  'appt_offset' => 3,  'sign_offset' => 8,  'deposit_offset' => 10],
            ['user' => $john, 'months_ago' => 3, 'client' => 'Chen Property',           'value' => 18500, 'gm' => 41, 'status' => 'closed_won',  'appt_offset' => 5,  'sign_offset' => 7,  'deposit_offset' => 9],
            ['user' => $john, 'months_ago' => 3, 'client' => 'Williams Driveway',       'value' => 12000, 'gm' => 38, 'status' => 'closed_lost', 'appt_offset' => 8,  'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $john, 'months_ago' => 3, 'client' => 'Sunset Gardens',          'value' => 28000, 'gm' => 45, 'status' => 'closed_won',  'appt_offset' => 10, 'sign_offset' => 12, 'deposit_offset' => 14],
            ['user' => $john, 'months_ago' => 3, 'client' => 'Patel Backyard',          'value' => 15000, 'gm' => 36, 'status' => 'closed_won',  'appt_offset' => 12, 'sign_offset' => 18, 'deposit_offset' => 20],

            ['user' => $jane, 'months_ago' => 3, 'client' => 'Thompson Pool Deck',     'value' => 42000, 'gm' => 46, 'status' => 'closed_won',  'appt_offset' => 2,  'sign_offset' => 4,  'deposit_offset' => 6],
            ['user' => $jane, 'months_ago' => 3, 'client' => 'Rivera Walkway',          'value' => 9500,  'gm' => 39, 'status' => 'closed_won',  'appt_offset' => 6,  'sign_offset' => 12, 'deposit_offset' => 14],
            ['user' => $jane, 'months_ago' => 3, 'client' => 'Kim Patio',               'value' => 21000, 'gm' => 42, 'status' => 'closed_lost', 'appt_offset' => 9,  'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $jane, 'months_ago' => 3, 'client' => 'Nguyen Courtyard',        'value' => 35000, 'gm' => 43, 'status' => 'closed_won',  'appt_offset' => 14, 'sign_offset' => 20, 'deposit_offset' => 22],

            // === 2 MONTHS AGO — Jane: great month, John: decent ===
            ['user' => $john, 'months_ago' => 2, 'client' => 'Baker Street Condos',     'value' => 67000, 'gm' => 43, 'status' => 'closed_won',  'appt_offset' => 1,  'sign_offset' => 6,  'deposit_offset' => 8],
            ['user' => $john, 'months_ago' => 2, 'client' => 'Greenfield Park',         'value' => 24000, 'gm' => 40, 'status' => 'closed_won',  'appt_offset' => 4,  'sign_offset' => 10, 'deposit_offset' => 12],
            ['user' => $john, 'months_ago' => 2, 'client' => 'Lopez Residence',         'value' => 19000, 'gm' => 37, 'status' => 'closed_lost', 'appt_offset' => 7,  'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $john, 'months_ago' => 2, 'client' => 'Harbor View Estate',      'value' => 44000, 'gm' => 47, 'status' => 'closed_won',  'appt_offset' => 10, 'sign_offset' => 12, 'deposit_offset' => 14],
            ['user' => $john, 'months_ago' => 2, 'client' => 'Singh Terrace',           'value' => 16000, 'gm' => 35, 'status' => 'closed_won',  'appt_offset' => 15, 'sign_offset' => 21, 'deposit_offset' => 23],

            ['user' => $jane, 'months_ago' => 2, 'client' => 'Westside Church',         'value' => 78000, 'gm' => 49, 'status' => 'closed_won',  'appt_offset' => 1,  'sign_offset' => 3,  'deposit_offset' => 5],
            ['user' => $jane, 'months_ago' => 2, 'client' => 'Anderson Patio',          'value' => 31000, 'gm' => 45, 'status' => 'closed_won',  'appt_offset' => 3,  'sign_offset' => 5,  'deposit_offset' => 7],
            ['user' => $jane, 'months_ago' => 2, 'client' => 'Cooper Driveway',         'value' => 22000, 'gm' => 41, 'status' => 'closed_won',  'appt_offset' => 6,  'sign_offset' => 9,  'deposit_offset' => 11],
            ['user' => $jane, 'months_ago' => 2, 'client' => 'Taylor Front Walk',       'value' => 14000, 'gm' => 38, 'status' => 'closed_won',  'appt_offset' => 10, 'sign_offset' => 15, 'deposit_offset' => 17],
            ['user' => $jane, 'months_ago' => 2, 'client' => 'Mitchell Retaining Wall', 'value' => 27000, 'gm' => 44, 'status' => 'closed_won',  'appt_offset' => 14, 'sign_offset' => 18, 'deposit_offset' => 20],
            ['user' => $jane, 'months_ago' => 2, 'client' => 'Davis Garage Pad',        'value' => 11000, 'gm' => 36, 'status' => 'closed_lost', 'appt_offset' => 18, 'sign_offset' => null, 'deposit_offset' => null],

            // === LAST MONTH — Both active, mix of statuses ===
            ['user' => $john, 'months_ago' => 1, 'client' => 'Pacific Heights Apt',     'value' => 95000, 'gm' => 50, 'status' => 'closed_won',  'appt_offset' => 1,  'sign_offset' => 2,  'deposit_offset' => 4],
            ['user' => $john, 'months_ago' => 1, 'client' => 'Monroe Residence',        'value' => 28000, 'gm' => 42, 'status' => 'closed_won',  'appt_offset' => 3,  'sign_offset' => 7,  'deposit_offset' => 9],
            ['user' => $john, 'months_ago' => 1, 'client' => 'Franklin Commercial',     'value' => 120000,'gm' => 46, 'status' => 'closed_won',  'appt_offset' => 5,  'sign_offset' => 8,  'deposit_offset' => 10],
            ['user' => $john, 'months_ago' => 1, 'client' => 'Reed Backyard',           'value' => 17000, 'gm' => 39, 'status' => 'closed_won',  'appt_offset' => 8,  'sign_offset' => 14, 'deposit_offset' => 16],
            ['user' => $john, 'months_ago' => 1, 'client' => 'Garcia Pool Area',        'value' => 33000, 'gm' => 44, 'status' => 'closed_lost', 'appt_offset' => 12, 'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $john, 'months_ago' => 1, 'client' => 'Lee Walkway',             'value' => 8500,  'gm' => 37, 'status' => 'closed_won',  'appt_offset' => 15, 'sign_offset' => 20, 'deposit_offset' => 22],

            ['user' => $jane, 'months_ago' => 1, 'client' => 'Riverside Community',     'value' => 85000, 'gm' => 47, 'status' => 'closed_won',  'appt_offset' => 2,  'sign_offset' => 4,  'deposit_offset' => 6],
            ['user' => $jane, 'months_ago' => 1, 'client' => 'White Residence',         'value' => 19000, 'gm' => 40, 'status' => 'closed_won',  'appt_offset' => 4,  'sign_offset' => 6,  'deposit_offset' => 8],
            ['user' => $jane, 'months_ago' => 1, 'client' => 'Park Side Office',        'value' => 52000, 'gm' => 45, 'status' => 'closed_won',  'appt_offset' => 7,  'sign_offset' => 10, 'deposit_offset' => 12],
            ['user' => $jane, 'months_ago' => 1, 'client' => 'Young Driveway',          'value' => 14500, 'gm' => 36, 'status' => 'closed_lost', 'appt_offset' => 10, 'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $jane, 'months_ago' => 1, 'client' => 'Harris Patio',            'value' => 26000, 'gm' => 43, 'status' => 'closed_won',  'appt_offset' => 14, 'sign_offset' => 17, 'deposit_offset' => 19],

            // === CURRENT MONTH — Active pipeline ===
            ['user' => $john, 'months_ago' => 0, 'client' => 'Oakwood Mall Expansion',  'value' => 145000,'gm' => 48, 'status' => 'closed_won',  'appt_offset' => 1,  'sign_offset' => 2,  'deposit_offset' => 4],
            ['user' => $john, 'months_ago' => 0, 'client' => 'Peterson Patio',          'value' => 22000, 'gm' => 43, 'status' => 'closed_won',  'appt_offset' => 2,  'sign_offset' => 5,  'deposit_offset' => 7],
            ['user' => $john, 'months_ago' => 0, 'client' => 'Valley View Apartments',  'value' => 68000, 'gm' => 45, 'status' => 'quote_sent',  'appt_offset' => 4,  'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $john, 'months_ago' => 0, 'client' => 'Brooks Residence',        'value' => 31000, 'gm' => 41, 'status' => 'appointment_set', 'appt_offset' => 6, 'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $john, 'months_ago' => 0, 'client' => 'Campbell Driveway',       'value' => 15000, 'gm' => 39, 'status' => 'lead',        'appt_offset' => 8,    'sign_offset' => null, 'deposit_offset' => null],

            ['user' => $jane, 'months_ago' => 0, 'client' => 'Sunrise Senior Living',   'value' => 110000,'gm' => 51, 'status' => 'closed_won',  'appt_offset' => 1,  'sign_offset' => 3,  'deposit_offset' => 5],
            ['user' => $jane, 'months_ago' => 0, 'client' => 'Morgan Family Home',      'value' => 38000, 'gm' => 44, 'status' => 'closed_won',  'appt_offset' => 3,  'sign_offset' => 5,  'deposit_offset' => 7],
            ['user' => $jane, 'months_ago' => 0, 'client' => 'Hilltop Church',          'value' => 56000, 'gm' => 46, 'status' => 'quote_sent',  'appt_offset' => 5,  'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $jane, 'months_ago' => 0, 'client' => 'Nelson Courtyard',        'value' => 24000, 'gm' => 40, 'status' => 'appointment_set', 'appt_offset' => 7, 'sign_offset' => null, 'deposit_offset' => null],
            ['user' => $jane, 'months_ago' => 0, 'client' => 'Fox Commercial Plaza',    'value' => 92000, 'gm' => 47, 'status' => 'lead',        'appt_offset' => 9,    'sign_offset' => null, 'deposit_offset' => null],
        ];

        $fastCloseDays = (int) CommissionSetting::getValue('fast_close_days', 3);

        foreach ($deals as $d) {
            $monthStart = now()->subMonths($d['months_ago'])->startOfMonth();

            $apptDate = $d['appt_offset'] !== null
                ? $monthStart->copy()->addDays($d['appt_offset'])
                : null;

            $signDate = $d['sign_offset'] !== null
                ? $monthStart->copy()->addDays($d['sign_offset'])
                : null;

            $depositDate = $d['deposit_offset'] !== null
                ? $monthStart->copy()->addDays($d['deposit_offset'])
                : null;

            $daysToClose = ($apptDate && $signDate)
                ? $apptDate->diffInDays($signDate)
                : null;

            $isFastClose = $daysToClose !== null && $daysToClose <= $fastCloseDays;

            Deal::create([
                'user_id' => $d['user']->id,
                'month' => $monthStart->format('Y-m-d'),
                'client_name' => $d['client'],
                'appointment_date' => $apptDate,
                'contract_signed_date' => $signDate,
                'deposit_date' => $depositDate,
                'original_contract_price' => $d['value'] + rand(0, 5000),
                'sold_contract_value' => $d['value'],
                'estimated_gm_percent' => $d['gm'],
                'deal_status' => $d['status'],
                'days_to_close' => $daysToClose,
                'is_fast_close' => $isFastClose,
                'notes' => $d['status'] === 'closed_lost' ? 'Client went with a competitor.' : null,
            ]);
        }
    }

    protected function calculateCommissions(): void
    {
        $service = new CommissionCalculatorService();

        $closedDeals = Deal::where('deal_status', DealStatus::ClosedWon)->get();

        foreach ($closedDeals as $deal) {
            $result = $service->calculate(
                (float) $deal->sold_contract_value,
                (float) $deal->estimated_gm_percent,
                $deal->is_fast_close
            );

            CommissionPayout::create([
                'deal_id' => $deal->id,
                'user_id' => $deal->user_id,
                'month' => $deal->month,
                'sold_contract_value' => $result['contract_value'],
                'gm_percent' => $result['gm_percent'],
                'tier' => $result['tier'],
                'base_commission' => $result['base_commission'],
                'surplus_bonus' => $result['surplus_bonus'],
                'fast_close_bonus' => $result['fast_close_bonus'],
                'total_payout' => $result['total_payout'],
                'settings_snapshot' => $result['settings_snapshot'],
            ]);
        }
    }

    protected function generateWeeklyScores(): void
    {
        $reps = User::where('role', 'sales_rep')->get();

        // Generate scores for last 8 weeks
        for ($w = 7; $w >= 0; $w--) {
            $weekStart = now()->subWeeks($w)->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

            foreach ($reps as $rep) {
                $appointmentDeals = Deal::where('user_id', $rep->id)
                    ->whereBetween('appointment_date', [$weekStart, $weekEnd])
                    ->count();

                $closedDeals = Deal::where('user_id', $rep->id)
                    ->where('deal_status', DealStatus::ClosedWon)
                    ->whereBetween('contract_signed_date', [$weekStart, $weekEnd])
                    ->get();

                $dealsClosed = $closedDeals->count();
                $closeRate = $appointmentDeals > 0
                    ? round(($dealsClosed / $appointmentDeals) * 100, 2)
                    : 0;

                $avgDays = $closedDeals->whereNotNull('days_to_close')->count() > 0
                    ? round($closedDeals->whereNotNull('days_to_close')->avg('days_to_close'), 1)
                    : 0;

                $fastCloses = $closedDeals->where('is_fast_close', true)->count();

                // Only create if there's some activity
                if ($appointmentDeals > 0 || $dealsClosed > 0) {
                    WeeklyScore::create([
                        'user_id' => $rep->id,
                        'week_start' => $weekStart->format('Y-m-d'),
                        'week_end' => $weekEnd->format('Y-m-d'),
                        'appointments' => $appointmentDeals,
                        'quotes_sent' => max(0, $appointmentDeals - rand(0, 1)),
                        'deals_closed' => $dealsClosed,
                        'close_rate' => $closeRate,
                        'avg_days_to_close' => $avgDays,
                        'fast_closes' => $fastCloses,
                    ]);
                }
            }
        }
    }

    protected function calculateSpiffs(): void
    {
        $service = new SpiffCalculatorService();

        // Calculate for last 3 months + current
        for ($m = 3; $m >= 0; $m--) {
            $month = now()->subMonths($m)->format('Y-m');
            $monthDate = $month . '-01';
            $results = $service->calculateMonth($month);

            foreach ($results as $userId => $data) {
                SpiffPayout::create([
                    'user_id' => $userId,
                    'month' => $monthDate,
                    'appointments' => $data['appointments'],
                    'deals_closed' => $data['deals_closed'],
                    'close_rate' => $data['close_rate'],
                    'prior_close_rate' => $data['prior_close_rate'],
                    'improvement_points' => $data['improvement_points'],
                    'improvement_bonus' => $data['improvement_bonus'],
                    'target_bonus' => $data['target_bonus'],
                    'fast_close_count' => $data['fast_close_count'],
                    'fast_close_bonus' => $data['fast_close_bonus'],
                    'highest_close_rate_bonus' => $data['highest_close_rate_bonus'],
                    'total_spiff' => $data['total_spiff'],
                    'is_override' => false,
                    'settings_snapshot' => $data['settings_snapshot'],
                ]);
            }
        }
    }

    protected function seedAuditLogs(User $john, User $jane): void
    {
        $admin = User::where('email', 'admin@baysidepavers.com')->first();

        $entries = [
            ['user_id' => $admin->id, 'action' => 'user_created', 'type' => User::class, 'id' => $john->id, 'ago' => 30],
            ['user_id' => $admin->id, 'action' => 'user_created', 'type' => User::class, 'id' => $jane->id, 'ago' => 30],
            ['user_id' => $admin->id, 'action' => 'commission_settings_updated', 'type' => \App\Models\CommissionSetting::class, 'id' => 1, 'ago' => 25],
            ['user_id' => $john->id,  'action' => 'deal_created', 'type' => Deal::class, 'id' => 1, 'ago' => 20],
            ['user_id' => $jane->id,  'action' => 'deal_created', 'type' => Deal::class, 'id' => 2, 'ago' => 18],
            ['user_id' => $admin->id, 'action' => 'branding_updated', 'type' => \App\Models\BrandingSetting::class, 'id' => 1, 'ago' => 15],
            ['user_id' => $john->id,  'action' => 'deal_status_changed', 'type' => Deal::class, 'id' => 1, 'ago' => 10],
            ['user_id' => $jane->id,  'action' => 'deal_status_changed', 'type' => Deal::class, 'id' => 2, 'ago' => 8],
            ['user_id' => $admin->id, 'action' => 'password_reset', 'type' => User::class, 'id' => $john->id, 'ago' => 5],
            ['user_id' => $admin->id, 'action' => 'spiff_settings_updated', 'type' => \App\Models\SpiffSetting::class, 'id' => 1, 'ago' => 3],
            ['user_id' => $john->id,  'action' => 'deal_created', 'type' => Deal::class, 'id' => 3, 'ago' => 2],
            ['user_id' => $jane->id,  'action' => 'deal_created', 'type' => Deal::class, 'id' => 4, 'ago' => 1],
        ];

        foreach ($entries as $e) {
            AuditLog::create([
                'user_id' => $e['user_id'],
                'action' => $e['action'],
                'auditable_type' => $e['type'],
                'auditable_id' => $e['id'],
                'old_values' => null,
                'new_values' => null,
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subDays($e['ago']),
            ]);
        }
    }
}
