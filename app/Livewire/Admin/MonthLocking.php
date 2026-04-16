<?php

namespace App\Livewire\Admin;

use App\Enums\DealStatus;
use App\Models\AuditLog;
use App\Models\CommissionSetting;
use App\Models\Deal;
use App\Models\MonthlySnapshot;
use App\Models\SpiffPayout;
use App\Models\SpiffSetting;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class MonthLocking extends Component
{
    public bool $showChecklist = false;
    public string $checklistMonth = '';
    public array $checklist = [];

    public function prepLock(string $month): void
    {
        $monthDate = Carbon::parse($month . '-01');

        $deals = Deal::whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->get();

        $openDeals = $deals->whereNotIn('deal_status', [DealStatus::ClosedWon, DealStatus::ClosedLost])->count();
        $missingApptDate = $deals->whereNull('appointment_date')->count();
        $closedWonMissingSigned = $deals->where('deal_status', DealStatus::ClosedWon)->whereNull('contract_signed_date')->count();

        $repCount = User::where('role', 'sales_rep')->where('is_active', true)->count();
        $spiffCount = SpiffPayout::whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)->count();
        $spiffNotCalculated = $spiffCount < $repCount;

        $this->checklist = [
            'month_label' => $monthDate->format('F Y'),
            'total_deals' => $deals->count(),
            'open_deals' => $openDeals,
            'missing_appt_date' => $missingApptDate,
            'closed_won_missing_signed' => $closedWonMissingSigned,
            'spiff_not_calculated' => $spiffNotCalculated,
            'spiff_count' => $spiffCount,
            'rep_count' => $repCount,
            'all_clear' => $openDeals === 0 && $missingApptDate === 0 && $closedWonMissingSigned === 0 && !$spiffNotCalculated,
        ];

        $this->checklistMonth = $month;
        $this->showChecklist = true;
    }

    public function confirmLock(): void
    {
        $this->showChecklist = false;
        $this->lock($this->checklistMonth);
    }

    public function lock(string $month): void
    {
        $monthDate = $month . '-01';

        if (MonthlySnapshot::isMonthLocked($monthDate)) {
            $this->dispatch('toast', type: 'error', message: 'This month is already locked.');
            return;
        }

        $snapshot = MonthlySnapshot::updateOrCreate(
            ['month' => $monthDate],
            [
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => auth()->id(),
                'unlocked_at' => null,
                'unlocked_by' => null,
                'commission_settings_snapshot' => CommissionSetting::allAsArray(),
                'spiff_settings_snapshot' => SpiffSetting::allAsArray(),
            ]
        );

        AuditLog::record('month_locked', $snapshot, null, [
            'month' => $month,
            'locked_by' => auth()->user()->name,
        ]);

        $this->dispatch('toast', type: 'success', message: Carbon::parse($monthDate)->format('F Y') . ' has been locked.');
    }

    public function unlock(string $month): void
    {
        $monthDate = $month . '-01';
        $snapshot = MonthlySnapshot::where('month', $monthDate)->first();

        if (!$snapshot || !$snapshot->is_locked) {
            $this->dispatch('toast', type: 'error', message: 'This month is not locked.');
            return;
        }

        $snapshot->update([
            'is_locked' => false,
            'unlocked_at' => now(),
            'unlocked_by' => auth()->id(),
        ]);

        AuditLog::record('month_unlocked', $snapshot, null, [
            'month' => $month,
            'unlocked_by' => auth()->user()->name,
        ]);

        $this->dispatch('toast', type: 'success', message: Carbon::parse($monthDate)->format('F Y') . ' has been unlocked.');
    }

    public function render()
    {
        // Show last 12 months
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i)->startOfMonth();
            $monthStr = $date->format('Y-m');
            $snapshot = MonthlySnapshot::where('month', $date->format('Y-m-d'))->first();

            $months[] = [
                'month' => $monthStr,
                'label' => $date->format('F Y'),
                'is_locked' => $snapshot?->is_locked ?? false,
                'locked_at' => $snapshot?->locked_at,
                'locked_by' => $snapshot?->lockedByUser?->name,
                'unlocked_at' => $snapshot?->unlocked_at,
                'unlocked_by' => $snapshot?->unlockedByUser?->name,
                'has_snapshot' => $snapshot !== null,
            ];
        }

        return view('livewire.admin.month-locking', [
            'months' => $months,
        ])->layout('layouts.app')->title('Month Locking');
    }
}
