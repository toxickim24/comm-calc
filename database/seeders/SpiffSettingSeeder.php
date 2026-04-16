<?php

namespace Database\Seeders;

use App\Models\SpiffSetting;
use Illuminate\Database\Seeder;

class SpiffSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'improvement_bonus',
                'value' => 500.0000,
                'label' => 'Improvement Bonus ($)',
                'description' => 'Bonus for improving close rate by threshold amount',
            ],
            [
                'key' => 'improvement_min_appts',
                'value' => 10.0000,
                'label' => 'Improvement Min Appointments',
                'description' => 'Minimum appointments required for improvement bonus',
            ],
            [
                'key' => 'improvement_min_points',
                'value' => 5.0000,
                'label' => 'Improvement Min Points (pp)',
                'description' => 'Minimum percentage point improvement required',
            ],
            [
                'key' => 'target_20_bonus',
                'value' => 500.0000,
                'label' => 'Target 20%+ Bonus ($)',
                'description' => 'Bonus for achieving 20%+ close rate',
            ],
            [
                'key' => 'target_30_bonus',
                'value' => 1000.0000,
                'label' => 'Target 30%+ Bonus ($)',
                'description' => 'Bonus for achieving 30%+ close rate (replaces 20% bonus)',
            ],
            [
                'key' => 'target_min_appts',
                'value' => 12.0000,
                'label' => 'Target Min Appointments',
                'description' => 'Minimum appointments required for target bonus',
            ],
            [
                'key' => 'fast_close_per_deal',
                'value' => 250.0000,
                'label' => 'Fast Close Per Deal ($)',
                'description' => 'SPIFF bonus per fast-closed deal',
            ],
            [
                'key' => 'highest_close_rate_bonus',
                'value' => 500.0000,
                'label' => 'Highest Close Rate Bonus ($)',
                'description' => 'Bonus for the rep with highest close rate',
            ],
            [
                'key' => 'tie_handling',
                'value' => 1.0000,
                'label' => 'Tie Handling Rule',
                'description' => '1 = All tied reps get bonus, 2 = Split bonus, 3 = No bonus on tie',
            ],
        ];

        foreach ($settings as $setting) {
            SpiffSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
