<?php

namespace App\Livewire;

use App\Enums\DealStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\CommissionPayout;
use App\Models\Deal;
use App\Models\SpiffPayout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $currentMonth = now()->format('Y-m');
        $isRep = $user->isSalesRep();

        // Base stats
        $stats = $this->getStats($user, $isRep);

        // Monthly commission data (last 6 months)
        $commissionTrend = $this->getCommissionTrend($user, $isRep);

        // Deal pipeline
        $pipeline = $this->getPipeline($user, $isRep);

        // Leaderboard (admin/manager only)
        $leaderboard = !$isRep ? $this->getLeaderboard($currentMonth) : null;

        // Recent activity
        $recentActivity = $this->getRecentActivity($user, $isRep);

        // Year-to-date summary
        $ytdSummary = $this->getYtdSummary($user, $isRep);

        return view('livewire.dashboard', compact(
            'stats', 'commissionTrend', 'pipeline', 'leaderboard', 'recentActivity', 'ytdSummary'
        ))
            ->layout('layouts.app')
            ->title('Dashboard');
    }

    protected function getStats($user, bool $isRep): array
    {
        $currentMonth = now()->startOfMonth();

        $dealsQuery = $isRep
            ? Deal::where('user_id', $user->id)
            : Deal::query();

        $commissionsQuery = $isRep
            ? CommissionPayout::where('user_id', $user->id)
            : CommissionPayout::query();

        $monthDeals = (clone $dealsQuery)
            ->whereYear('month', $currentMonth->year)
            ->whereMonth('month', $currentMonth->month);

        $monthCommissions = (clone $commissionsQuery)
            ->whereYear('month', $currentMonth->year)
            ->whereMonth('month', $currentMonth->month);

        return [
            'total_users' => $user->isAdmin() ? User::count() : null,
            'active_reps' => !$isRep
                ? User::where('role', UserRole::SalesRep)->where('is_active', true)->count()
                : null,
            'month_deals' => (clone $monthDeals)->count(),
            'month_closed' => (clone $monthDeals)->where('deal_status', DealStatus::ClosedWon)->count(),
            'month_commission' => (clone $monthCommissions)->sum('total_payout'),
            'month_pipeline_value' => (clone $monthDeals)
                ->whereNotIn('deal_status', [DealStatus::ClosedLost])
                ->sum('sold_contract_value'),
        ];
    }

    protected function getCommissionTrend($user, bool $isRep): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $query = CommissionPayout::whereYear('month', $month->year)
                ->whereMonth('month', $month->month);

            if ($isRep) {
                $query->where('user_id', $user->id);
            }

            $months[] = [
                'label' => $month->format('M'),
                'total' => round((float) $query->sum('total_payout'), 2),
            ];
        }

        return $months;
    }

    protected function getPipeline($user, bool $isRep): array
    {
        $query = Deal::whereYear('month', now()->year)
            ->whereMonth('month', now()->month);

        if ($isRep) {
            $query->where('user_id', $user->id);
        }

        $deals = $query->get();

        return [
            ['label' => 'Lead', 'count' => $deals->where('deal_status', DealStatus::Lead)->count(), 'color' => 'gray'],
            ['label' => 'Appt Set', 'count' => $deals->where('deal_status', DealStatus::AppointmentSet)->count(), 'color' => 'blue'],
            ['label' => 'Quote Sent', 'count' => $deals->where('deal_status', DealStatus::QuoteSent)->count(), 'color' => 'yellow'],
            ['label' => 'Closed Won', 'count' => $deals->where('deal_status', DealStatus::ClosedWon)->count(), 'color' => 'green'],
            ['label' => 'Closed Lost', 'count' => $deals->where('deal_status', DealStatus::ClosedLost)->count(), 'color' => 'red'],
        ];
    }

    protected function getLeaderboard(string $month): array
    {
        $monthDate = Carbon::parse($month . '-01');

        // Pre-fetch commission totals and deal counts to avoid N+1
        $commissions = CommissionPayout::selectRaw('user_id, SUM(total_payout) as total')
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $dealCounts = Deal::selectRaw('user_id, COUNT(*) as total')
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->where('deal_status', DealStatus::ClosedWon)
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return User::where('role', UserRole::SalesRep)
            ->where('is_active', true)
            ->get()
            ->map(fn ($rep) => [
                'name' => $rep->name,
                'commission' => round((float) ($commissions[$rep->id] ?? 0), 2),
                'deals_closed' => (int) ($dealCounts[$rep->id] ?? 0),
            ])
            ->sortByDesc('commission')
            ->values()
            ->toArray();
    }

    protected function getRecentActivity($user, bool $isRep): array
    {
        $query = AuditLog::with('user')
            ->whereIn('action', [
                'deal_created', 'deal_updated', 'deal_status_changed', 'deal_deleted',
                'user_created', 'user_updated', 'user_deleted',
                'commission_settings_updated', 'spiff_settings_updated',
                'spiff_override', 'branding_updated', 'password_reset',
            ])
            ->orderByDesc('created_at')
            ->limit(10);

        return $query->get()->map(function ($log) {
            return [
                'action' => $this->formatAction($log->action),
                'user' => $log->user?->name ?? 'System',
                'time' => $log->created_at->diffForHumans(),
                'icon' => $this->actionIcon($log->action),
                'color' => $this->actionColor($log->action),
            ];
        })->toArray();
    }

    protected function getYtdSummary($user, bool $isRep): array
    {
        $year = now()->year;

        if ($isRep) {
            // Single rep YTD
            $commission = CommissionPayout::where('user_id', $user->id)
                ->whereYear('month', $year)->sum('total_payout');
            $spiff = SpiffPayout::where('user_id', $user->id)
                ->whereYear('month', $year)->sum('total_spiff');

            return [[
                'name' => $user->name,
                'commission' => round((float) $commission, 2),
                'spiff' => round((float) $spiff, 2),
                'total' => round((float) $commission + (float) $spiff, 2),
            ]];
        }

        // All reps YTD (admin/manager)
        $commissions = CommissionPayout::selectRaw('user_id, SUM(total_payout) as total')
            ->whereYear('month', $year)->groupBy('user_id')->pluck('total', 'user_id');
        $spiffs = SpiffPayout::selectRaw('user_id, SUM(total_spiff) as total')
            ->whereYear('month', $year)->groupBy('user_id')->pluck('total', 'user_id');

        return User::where('role', UserRole::SalesRep)->where('is_active', true)
            ->orderBy('name')->get()
            ->map(function ($rep) use ($commissions, $spiffs) {
                $c = round((float) ($commissions[$rep->id] ?? 0), 2);
                $s = round((float) ($spiffs[$rep->id] ?? 0), 2);
                return [
                    'id' => $rep->id,
                    'name' => $rep->name,
                    'commission' => $c,
                    'spiff' => $s,
                    'total' => round($c + $s, 2),
                ];
            })->toArray();
    }

    protected function formatAction(string $action): string
    {
        return match ($action) {
            'deal_created' => 'Created a new deal',
            'deal_updated' => 'Updated a deal',
            'deal_status_changed' => 'Changed deal status',
            'deal_deleted' => 'Deleted a deal',
            'user_created' => 'Created a new user',
            'user_updated' => 'Updated a user',
            'user_deleted' => 'Deleted a user',
            'commission_settings_updated' => 'Updated commission settings',
            'spiff_settings_updated' => 'Updated SPIFF settings',
            'spiff_override' => 'Applied SPIFF override',
            'branding_updated' => 'Updated branding',
            'password_reset' => 'Reset a password',
            default => str_replace('_', ' ', $action),
        };
    }

    protected function actionIcon(string $action): string
    {
        if (str_contains($action, 'deal')) return 'deal';
        if (str_contains($action, 'user') || str_contains($action, 'password')) return 'user';
        if (str_contains($action, 'settings') || str_contains($action, 'branding')) return 'settings';
        if (str_contains($action, 'spiff')) return 'money';
        return 'default';
    }

    protected function actionColor(string $action): string
    {
        if (str_contains($action, 'created')) return 'green';
        if (str_contains($action, 'deleted')) return 'red';
        if (str_contains($action, 'updated') || str_contains($action, 'changed')) return 'blue';
        if (str_contains($action, 'override')) return 'amber';
        return 'gray';
    }
}
