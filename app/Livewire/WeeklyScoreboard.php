<?php

namespace App\Livewire;

use App\Enums\DealStatus;
use App\Models\AuditLog;
use App\Models\CommissionSetting;
use App\Models\Deal;
use App\Models\User;
use App\Models\WeeklyScore;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class WeeklyScoreboard extends Component
{
    // Inline editing
    public ?int $editingScoreId = null;
    public string $editField = '';
    public string $editValue = '';
    public string $weekStart = '';
    public string $sortBy = 'deals_closed';
    public string $sortDir = 'desc';

    public function mount(): void
    {
        // Default to current week (Monday start)
        $this->weekStart = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    }

    public function previousWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->format('Y-m-d');
    }

    public function nextWeek(): void
    {
        $next = Carbon::parse($this->weekStart)->addWeek();
        if ($next->lte(now()->endOfWeek(Carbon::SUNDAY))) {
            $this->weekStart = $next->format('Y-m-d');
        }
    }

    public function currentWeek(): void
    {
        $this->weekStart = now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'desc';
        }
    }

    public function recalculate(): void
    {
        $weekStart = Carbon::parse($this->weekStart);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $fastCloseDays = (int) CommissionSetting::getValue('fast_close_days', 3);

        $reps = User::where('role', 'sales_rep')->get();

        foreach ($reps as $rep) {
            // Appointments: deals with appointment_date in this week
            $appointmentDeals = Deal::where('user_id', $rep->id)
                ->whereBetween('appointment_date', [$weekStart, $weekEnd])
                ->count();

            // Quotes: deals with status QuoteSent (or beyond) that had appointment this week
            $quotesSent = Deal::where('user_id', $rep->id)
                ->whereIn('deal_status', [DealStatus::QuoteSent, DealStatus::ClosedWon, DealStatus::ClosedLost])
                ->whereBetween('appointment_date', [$weekStart, $weekEnd])
                ->count();

            // Closed Won deals with contract signed this week
            $dealsClosed = Deal::where('user_id', $rep->id)
                ->where('deal_status', DealStatus::ClosedWon)
                ->whereBetween('contract_signed_date', [$weekStart, $weekEnd])
                ->count();

            $closeRate = $appointmentDeals > 0
                ? round(($dealsClosed / $appointmentDeals) * 100, 2)
                : 0;

            $closedDeals = Deal::where('user_id', $rep->id)
                ->where('deal_status', DealStatus::ClosedWon)
                ->whereBetween('contract_signed_date', [$weekStart, $weekEnd])
                ->whereNotNull('days_to_close')
                ->get();

            $avgDaysToClose = $closedDeals->count() > 0
                ? round($closedDeals->avg('days_to_close'), 1)
                : 0;

            $fastCloses = $closedDeals->where('is_fast_close', true)->count();

            WeeklyScore::updateOrCreate(
                [
                    'user_id' => $rep->id,
                    'week_start' => $weekStart->format('Y-m-d'),
                ],
                [
                    'week_end' => $weekEnd->format('Y-m-d'),
                    'appointments' => $appointmentDeals,
                    'quotes_sent' => $quotesSent,
                    'deals_closed' => $dealsClosed,
                    'close_rate' => $closeRate,
                    'avg_days_to_close' => $avgDaysToClose,
                    'fast_closes' => $fastCloses,
                ]
            );
        }

        $this->dispatch('toast', type: 'success', message: 'Scoreboard recalculated for this week.');
    }

    public function startEdit(int $scoreId, string $field): void
    {
        $user = auth()->user();
        if ($user->isSalesRep()) return;

        $score = WeeklyScore::find($scoreId);
        if (!$score) return;

        $this->editingScoreId = $scoreId;
        $this->editField = $field;
        $this->editValue = (string) $score->{$field};
    }

    public function saveEdit(): void
    {
        if (!$this->editingScoreId || !$this->editField) return;

        $score = WeeklyScore::find($this->editingScoreId);
        if (!$score) return;

        $allowed = ['appointments', 'quotes_sent', 'deals_closed', 'close_rate', 'avg_days_to_close', 'fast_closes'];
        if (!in_array($this->editField, $allowed)) return;

        $oldValue = $score->{$this->editField};
        $newValue = is_numeric($this->editValue) ? (float) $this->editValue : 0;

        if ((float) $oldValue !== $newValue) {
            $score->update([$this->editField => $newValue]);
            AuditLog::record('weekly_score_edited', $score, [
                $this->editField => $oldValue,
            ], [
                $this->editField => $newValue,
                'rep' => $score->user->name,
                'week' => $score->week_start->format('M d'),
            ]);
        }

        $this->cancelEdit();
        $this->dispatch('toast', type: 'success', message: 'Score updated.');
    }

    public function cancelEdit(): void
    {
        $this->editingScoreId = null;
        $this->editField = '';
        $this->editValue = '';
    }

    public function render()
    {
        $user = auth()->user();
        $weekStart = Carbon::parse($this->weekStart);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $isCurrentWeek = now()->between($weekStart, $weekEnd);

        $query = WeeklyScore::with('user')
            ->where('week_start', $weekStart->format('Y-m-d'));

        // Sales reps only see their own scores
        if ($user->isSalesRep()) {
            $query->where('user_id', $user->id);
        }

        $scores = $query->orderBy($this->sortBy, $this->sortDir)->get();

        // Calculate team totals for admin/manager
        $teamTotals = null;
        if (!$user->isSalesRep() && $scores->isNotEmpty()) {
            $teamTotals = [
                'appointments' => $scores->sum('appointments'),
                'quotes_sent' => $scores->sum('quotes_sent'),
                'deals_closed' => $scores->sum('deals_closed'),
                'close_rate' => $scores->avg('close_rate'),
                'avg_days_to_close' => $scores->where('avg_days_to_close', '>', 0)->avg('avg_days_to_close') ?? 0,
                'fast_closes' => $scores->sum('fast_closes'),
            ];
        }

        return view('livewire.weekly-scoreboard', [
            'scores' => $scores,
            'weekStartDate' => $weekStart,
            'weekEndDate' => $weekEnd,
            'isCurrentWeek' => $isCurrentWeek,
            'teamTotals' => $teamTotals,
        ])->layout('layouts.app')->title('Weekly Scoreboard');
    }
}
