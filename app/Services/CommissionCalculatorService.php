<?php

namespace App\Services;

use App\Models\CommissionSetting;

class CommissionCalculatorService
{
    protected array $settings;

    protected array $tiers;

    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? CommissionSetting::allAsArray();
        $this->buildTiers();
    }

    protected function buildTiers(): void
    {
        $this->tiers = [
            ['min' => 35.0, 'max' => 35.0,  'rate' => (float) ($this->settings['floor_percent'] ?? 0.5),        'label' => 'Floor (35%)'],
            ['min' => 35.1, 'max' => 37.9,   'rate' => (float) ($this->settings['tier_35_1_37_9_rate'] ?? 3.0),   'label' => '35.1% – 37.9%'],
            ['min' => 38.0, 'max' => 40.9,   'rate' => (float) ($this->settings['tier_38_40_9_rate'] ?? 5.0),     'label' => '38% – 40.9%'],
            ['min' => 41.0, 'max' => 43.9,   'rate' => (float) ($this->settings['tier_41_43_9_rate'] ?? 7.0),     'label' => '41% – 43.9%'],
            ['min' => 44.0, 'max' => 46.9,   'rate' => (float) ($this->settings['tier_44_46_9_rate'] ?? 9.0),     'label' => '44% – 46.9%'],
            ['min' => 47.0, 'max' => 100.0,  'rate' => (float) ($this->settings['tier_47_rate'] ?? 10.0),         'label' => '47%+'],
        ];
    }

    public function calculate(float $contractValue, float $gmPercent, bool $isFastClose = false): array
    {
        $minGm = (float) ($this->settings['min_gm_percent'] ?? 35.0);
        $targetGm = (float) ($this->settings['target_gm_percent'] ?? 47.0);
        $floorMinAmount = (float) ($this->settings['floor_min_amount'] ?? 750.0);
        $surplusMultiplier = (float) ($this->settings['surplus_multiplier'] ?? 0.5);
        $fastCloseSpiff = (float) ($this->settings['fast_close_spiff'] ?? 250.0);

        // Below minimum GM or zero contract = no commission
        if ($gmPercent < $minGm || $contractValue <= 0) {
            return $this->buildResult($contractValue, $gmPercent, 'Below Floor', 0, 0, 0, 0, 0, $isFastClose);
        }

        // Find the matching tier
        $tier = $this->findTier($gmPercent);
        $tierLabel = $tier['label'];
        $tierRate = $tier['rate'] / 100;

        // Base commission
        $baseCommission = round($contractValue * $tierRate, 2);

        // Floor protection: at exactly 35% GM, minimum $750
        if ($gmPercent == $minGm) {
            $baseCommission = max($baseCommission, $floorMinAmount);
        }

        // Surplus bonus: extra commission for GM above target
        $surplusBonus = 0;
        if ($gmPercent > $targetGm) {
            $surplusPercent = ($gmPercent - $targetGm) / 100;
            $surplusBonus = round($contractValue * $surplusPercent * $surplusMultiplier, 2);
        }

        // Fast close bonus
        $fastCloseBonus = $isFastClose ? $fastCloseSpiff : 0;

        $totalPayout = round($baseCommission + $surplusBonus + $fastCloseBonus, 2);

        return $this->buildResult(
            $contractValue, $gmPercent, $tierLabel, $tierRate * 100,
            $baseCommission, $surplusBonus, $fastCloseBonus, $totalPayout, $isFastClose
        );
    }

    protected function findTier(float $gmPercent): array
    {
        foreach ($this->tiers as $tier) {
            if ($gmPercent >= $tier['min'] && $gmPercent <= $tier['max']) {
                return $tier;
            }
        }

        // Fallback to highest tier if somehow above 100
        return end($this->tiers);
    }

    protected function buildResult(
        float $contractValue,
        float $gmPercent,
        string $tierLabel,
        float $tierRate,
        float $baseCommission,
        float $surplusBonus,
        float $fastCloseBonus,
        float $totalPayout,
        bool $isFastClose
    ): array {
        return [
            'contract_value' => $contractValue,
            'gm_percent' => $gmPercent,
            'tier' => $tierLabel,
            'tier_rate' => $tierRate,
            'base_commission' => $baseCommission,
            'surplus_bonus' => $surplusBonus,
            'fast_close_bonus' => $fastCloseBonus,
            'total_payout' => $totalPayout,
            'is_fast_close' => $isFastClose,
            'settings_snapshot' => $this->settings,
        ];
    }

    public function getTiers(): array
    {
        return $this->tiers;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }
}
