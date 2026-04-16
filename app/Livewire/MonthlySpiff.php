<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\SpiffPayout;
use App\Models\SpiffSetting;
use App\Services\SpiffCalculatorService;
use Carbon\Carbon;
use Livewire\Component;

class MonthlySpiff extends Component
{
    public string $selectedMonth = '';

    // Override modal
    public bool $showOverrideModal = false;
    public ?int $overridePayoutId = null;
    public string $overrideRepName = '';
    public string $overrideTotal = '';
    public string $overrideNotes = '';

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    public function previousMonth(): void
    {
        $this->selectedMonth = Carbon::parse($this->selectedMonth . '-01')->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $next = Carbon::parse($this->selectedMonth . '-01')->addMonth();
        if ($next->lte(now()->endOfMonth())) {
            $this->selectedMonth = $next->format('Y-m');
        }
    }

    public function currentMonth(): void
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    public function calculate(): void
    {
        $service = new SpiffCalculatorService();
        $results = $service->calculateMonth($this->selectedMonth);
        $monthDate = $this->selectedMonth . '-01';

        foreach ($results as $userId => $data) {
            // Don't overwrite manual overrides
            $existing = SpiffPayout::where('user_id', $userId)
                ->whereYear('month', Carbon::parse($monthDate)->year)
                ->whereMonth('month', Carbon::parse($monthDate)->month)
                ->first();

            if ($existing && $existing->is_override) {
                continue;
            }

            SpiffPayout::updateOrCreate(
                [
                    'user_id' => $userId,
                    'month' => $monthDate,
                ],
                [
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
                    'override_notes' => null,
                    'settings_snapshot' => $data['settings_snapshot'],
                ]
            );
        }

        $this->dispatch('toast', type: 'success', message: 'SPIFF payouts calculated for ' . Carbon::parse($monthDate)->format('F Y') . '.');
    }

    public function openOverride(int $payoutId): void
    {
        $payout = SpiffPayout::with('user')->findOrFail($payoutId);
        $this->overridePayoutId = $payout->id;
        $this->overrideRepName = $payout->user->name;
        $this->overrideTotal = $payout->total_spiff;
        $this->overrideNotes = $payout->override_notes ?? '';
        $this->showOverrideModal = true;
    }

    public function saveOverride(): void
    {
        $this->validate([
            'overrideTotal' => 'required|numeric|min:0',
            'overrideNotes' => 'required|min:5|max:500',
        ], [
            'overrideNotes.required' => 'Override notes are required when manually adjusting.',
            'overrideNotes.min' => 'Please provide a brief explanation (at least 5 characters).',
        ]);

        $payout = SpiffPayout::findOrFail($this->overridePayoutId);
        $oldValues = ['total_spiff' => $payout->total_spiff, 'is_override' => $payout->is_override];

        $payout->update([
            'total_spiff' => $this->overrideTotal,
            'is_override' => true,
            'override_notes' => $this->overrideNotes,
        ]);

        AuditLog::record('spiff_override', $payout, $oldValues, [
            'total_spiff' => $this->overrideTotal,
            'override_notes' => $this->overrideNotes,
        ]);

        $this->showOverrideModal = false;
        $this->dispatch('toast', type: 'success', message: "SPIFF override saved for {$payout->user->name}.");
    }

    public function clearOverride(int $payoutId): void
    {
        $payout = SpiffPayout::findOrFail($payoutId);

        if (!$payout->is_override) return;

        AuditLog::record('spiff_override_cleared', $payout, [
            'total_spiff' => $payout->total_spiff,
            'override_notes' => $payout->override_notes,
        ]);

        // Recalculate this rep's payout
        $service = new SpiffCalculatorService();
        $results = $service->calculateMonth($this->selectedMonth);

        if (isset($results[$payout->user_id])) {
            $data = $results[$payout->user_id];
            $payout->update([
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
                'override_notes' => null,
                'settings_snapshot' => $data['settings_snapshot'],
            ]);
        }

        $this->dispatch('toast', type: 'success', message: 'Override cleared and recalculated.');
    }

    public function render()
    {
        $monthDate = Carbon::parse($this->selectedMonth . '-01');
        $isCurrentMonth = $monthDate->isSameMonth(now());

        $payouts = SpiffPayout::with('user')
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->orderByDesc('total_spiff')
            ->get();

        $grandTotal = $payouts->sum('total_spiff');

        return view('livewire.monthly-spiff', [
            'payouts' => $payouts,
            'monthDate' => $monthDate,
            'isCurrentMonth' => $isCurrentMonth,
            'grandTotal' => $grandTotal,
        ])->layout('layouts.app')->title('Monthly SPIFF');
    }
}
