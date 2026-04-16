<?php

namespace App\Services;

use App\Enums\DealStatus;
use App\Models\Deal;
use App\Models\SpiffSetting;
use App\Models\User;
use Carbon\Carbon;

class SpiffCalculatorService
{
    protected array $settings;

    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? SpiffSetting::allAsArray();
    }

    /**
     * Calculate SPIFF payouts for all reps for a given month.
     * Returns an array keyed by user_id.
     */
    public function calculateMonth(string $month): array
    {
        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $priorMonth = $monthStart->copy()->subMonth();

        $reps = User::where('role', 'sales_rep')->get();
        $results = [];

        // First pass: calculate individual stats
        foreach ($reps as $rep) {
            $results[$rep->id] = $this->calculateForRep($rep, $monthStart, $monthEnd, $priorMonth);
        }

        // Second pass: determine highest close rate bonus
        $this->applyHighestCloseRateBonus($results);

        // Recalculate totals after highest close rate bonus
        foreach ($results as &$result) {
            $result['total_spiff'] = round(
                $result['improvement_bonus'] +
                $result['target_bonus'] +
                $result['fast_close_bonus'] +
                $result['highest_close_rate_bonus'],
                2
            );
        }

        return $results;
    }

    protected function calculateForRep(User $rep, Carbon $monthStart, Carbon $monthEnd, Carbon $priorMonth): array
    {
        // Get current month deals
        $deals = Deal::where('user_id', $rep->id)
            ->whereYear('month', $monthStart->year)
            ->whereMonth('month', $monthStart->month)
            ->get();

        $appointments = $deals->whereNotNull('appointment_date')->count();
        $closedWon = $deals->where('deal_status', DealStatus::ClosedWon);
        $dealsClosed = $closedWon->count();
        $closeRate = $appointments > 0 ? round(($dealsClosed / $appointments) * 100, 2) : 0;
        $fastCloseCount = $closedWon->where('is_fast_close', true)->count();

        // Get prior month close rate
        $priorDeals = Deal::where('user_id', $rep->id)
            ->whereYear('month', $priorMonth->year)
            ->whereMonth('month', $priorMonth->month)
            ->get();

        $priorAppts = $priorDeals->whereNotNull('appointment_date')->count();
        $priorClosed = $priorDeals->where('deal_status', DealStatus::ClosedWon)->count();
        $priorCloseRate = $priorAppts > 0 ? round(($priorClosed / $priorAppts) * 100, 2) : 0;

        $improvementPoints = $closeRate - $priorCloseRate;

        // Calculate bonuses
        $improvementBonus = $this->calcImprovementBonus($improvementPoints, $appointments);
        $targetBonus = $this->calcTargetBonus($closeRate, $appointments);
        $fastCloseBonus = $this->calcFastCloseBonus($fastCloseCount);

        return [
            'user_id' => $rep->id,
            'user_name' => $rep->name,
            'appointments' => $appointments,
            'deals_closed' => $dealsClosed,
            'close_rate' => $closeRate,
            'prior_close_rate' => $priorCloseRate,
            'improvement_points' => $improvementPoints,
            'improvement_bonus' => $improvementBonus,
            'target_bonus' => $targetBonus,
            'fast_close_count' => $fastCloseCount,
            'fast_close_bonus' => $fastCloseBonus,
            'highest_close_rate_bonus' => 0, // Set in second pass
            'total_spiff' => 0, // Calculated after second pass
            'settings_snapshot' => $this->settings,
        ];
    }

    protected function calcImprovementBonus(float $improvementPoints, int $appointments): float
    {
        $minPoints = (float) ($this->settings['improvement_min_points'] ?? 5);
        $minAppts = (int) ($this->settings['improvement_min_appts'] ?? 10);
        $bonus = (float) ($this->settings['improvement_bonus'] ?? 500);

        if ($improvementPoints >= $minPoints && $appointments >= $minAppts) {
            return $bonus;
        }

        return 0;
    }

    protected function calcTargetBonus(float $closeRate, int $appointments): float
    {
        $target30Bonus = (float) ($this->settings['target_30_bonus'] ?? 1000);
        $target20Bonus = (float) ($this->settings['target_20_bonus'] ?? 500);
        $minAppts = (int) ($this->settings['target_min_appts'] ?? 12);

        // 30%+ takes priority (replaces 20% bonus)
        if ($closeRate >= 30 && $appointments >= $minAppts) {
            return $target30Bonus;
        }

        if ($closeRate >= 20) {
            return $target20Bonus;
        }

        return 0;
    }

    protected function calcFastCloseBonus(int $fastCloseCount): float
    {
        $perDeal = (float) ($this->settings['fast_close_per_deal'] ?? 250);
        return round($fastCloseCount * $perDeal, 2);
    }

    protected function applyHighestCloseRateBonus(array &$results): void
    {
        if (empty($results)) return;

        $bonus = (float) ($this->settings['highest_close_rate_bonus'] ?? 500);
        $tieHandling = (int) ($this->settings['tie_handling'] ?? 1);

        // Find the highest close rate among reps with at least 1 deal closed
        $eligible = array_filter($results, fn($r) => $r['deals_closed'] > 0);
        if (empty($eligible)) return;

        $maxRate = max(array_column($eligible, 'close_rate'));
        if ($maxRate <= 0) return;

        $winners = array_filter($eligible, fn($r) => $r['close_rate'] == $maxRate);
        $winnerCount = count($winners);

        foreach ($results as $userId => &$result) {
            if ($result['close_rate'] != $maxRate || $result['deals_closed'] <= 0) {
                continue;
            }

            if ($winnerCount === 1) {
                $result['highest_close_rate_bonus'] = $bonus;
            } else {
                // Tie handling
                switch ($tieHandling) {
                    case 1: // All tied reps get full bonus
                        $result['highest_close_rate_bonus'] = $bonus;
                        break;
                    case 2: // Split bonus
                        $result['highest_close_rate_bonus'] = round($bonus / $winnerCount, 2);
                        break;
                    case 3: // No bonus on tie
                        $result['highest_close_rate_bonus'] = 0;
                        break;
                }
            }
        }
    }

    public function getSettings(): array
    {
        return $this->settings;
    }
}
