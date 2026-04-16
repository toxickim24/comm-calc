<?php

namespace Database\Seeders;

use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

class CommissionSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'min_gm_percent',
                'value' => 35.0000,
                'label' => 'Minimum Gross Margin %',
                'description' => 'Below this threshold, commission is $0',
            ],
            [
                'key' => 'target_gm_percent',
                'value' => 47.0000,
                'label' => 'Target Gross Margin %',
                'description' => 'At or above this, rep earns max base rate plus surplus',
            ],
            [
                'key' => 'fast_close_spiff',
                'value' => 250.0000,
                'label' => 'Fast Close SPIFF ($)',
                'description' => 'Bonus for closing a deal within the fast close threshold',
            ],
            [
                'key' => 'fast_close_days',
                'value' => 3.0000,
                'label' => 'Fast Close Days',
                'description' => 'Maximum days to close for fast close eligibility',
            ],
            [
                'key' => 'floor_min_amount',
                'value' => 750.0000,
                'label' => '35% Floor Minimum ($)',
                'description' => 'Minimum payout when GM is exactly 35%',
            ],
            [
                'key' => 'floor_percent',
                'value' => 0.5000,
                'label' => '35% Floor Percent (%)',
                'description' => 'Percentage of sold value when GM is exactly 35%',
            ],
            [
                'key' => 'tier_35_1_37_9_rate',
                'value' => 3.0000,
                'label' => 'Tier 35.1%–37.9% Rate (%)',
                'description' => 'Commission rate for GM between 35.1% and 37.9%',
            ],
            [
                'key' => 'tier_38_40_9_rate',
                'value' => 5.0000,
                'label' => 'Tier 38%–40.9% Rate (%)',
                'description' => 'Commission rate for GM between 38% and 40.9%',
            ],
            [
                'key' => 'tier_41_43_9_rate',
                'value' => 7.0000,
                'label' => 'Tier 41%–43.9% Rate (%)',
                'description' => 'Commission rate for GM between 41% and 43.9%',
            ],
            [
                'key' => 'tier_44_46_9_rate',
                'value' => 9.0000,
                'label' => 'Tier 44%–46.9% Rate (%)',
                'description' => 'Commission rate for GM between 44% and 46.9%',
            ],
            [
                'key' => 'tier_47_rate',
                'value' => 10.0000,
                'label' => 'Tier 47%+ Rate (%)',
                'description' => 'Commission rate for GM at or above 47%',
            ],
            [
                'key' => 'surplus_multiplier',
                'value' => 0.5000,
                'label' => 'Surplus Multiplier',
                'description' => 'Multiplier for surplus bonus when GM exceeds target',
            ],
        ];

        foreach ($settings as $setting) {
            CommissionSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
