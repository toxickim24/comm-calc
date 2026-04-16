<?php

namespace App\Livewire;

use App\Enums\DealStatus;
use App\Models\CommissionSetting;
use App\Models\Deal;
use App\Models\User;
use App\Models\WeeklyScore;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class WeeklyScoreboard extends Component
{
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
            $deals = Deal::where('user_id', $rep->id)
                ->whereBetween('created_at', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
                ->get();

            $appointments = $deals->whereNotNull('appointment_date')
                ->where('appointment_date', '>=', $weekStart)
                ->where('appointment_date', '<=', $weekEnd)
                ->count();

            // Count deals where appointment_date is in this week as appointments
            // But also count deals with status appointment_set or beyond
            $appointmentDeals = Deal::where('user_id', $rep->id)
                ->whereBetween('appointment_date', [$weekStart, $weekEnd])
                ->count();

            $quotesSent = Deal::where('user_id', $rep->id)
                ->where('deal_status', DealStatus::QuoteSent)
                ->whereBetween('created_at', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
                ->count();

            // Also count closed deals that went through quote stage
            $quotesSent += Deal::where('user_id', $rep->id)
                ->whereIn('deal_status', [DealStatus::ClosedWon, DealStatus::ClosedLost])
                ->whereBetween('contract_signed_date', [$weekStart, $weekEnd])
                ->count();

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
