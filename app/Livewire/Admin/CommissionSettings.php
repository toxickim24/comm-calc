<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\CommissionSetting;
use Livewire\Component;

class CommissionSettings extends Component
{
    public array $settings = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $this->settings = CommissionSetting::all()
            ->keyBy('key')
            ->map(fn ($s) => [
                'id' => $s->id,
                'value' => $this->formatForDisplay($s->key, (float) $s->value),
                'label' => $s->label,
                'description' => $s->description,
            ])
            ->toArray();
    }

    protected function formatForDisplay(string $key, float $value): string
    {
        // Currency fields: whole dollars
        if (in_array($key, ['floor_min_amount', 'fast_close_spiff'])) {
            return (string) (int) $value;
        }

        // Whole-number percentages and counts
        if (in_array($key, ['min_gm_percent', 'target_gm_percent', 'fast_close_days',
            'tier_35_1_37_9_rate', 'tier_38_40_9_rate', 'tier_41_43_9_rate',
            'tier_44_46_9_rate', 'tier_47_rate'])) {
            return $value == (int) $value ? (string) (int) $value : rtrim(rtrim(number_format($value, 4), '0'), '.');
        }

        // Decimal values (floor_percent 0.5, surplus_multiplier 0.5): strip trailing zeros
        return rtrim(rtrim(number_format($value, 4), '0'), '.');
    }

    public function save(): void
    {
        $this->validate($this->validationRules());

        $changes = [];

        foreach ($this->settings as $key => $data) {
            $setting = CommissionSetting::find($data['id']);
            if (!$setting) continue;

            $oldValue = $setting->value;
            $newValue = (float) $data['value'];

            if ((float) $oldValue !== $newValue) {
                $changes[$key] = ['from' => $oldValue, 'to' => $newValue];
                $setting->update(['value' => $newValue]);
            }
        }

        if (!empty($changes)) {
            $firstSetting = CommissionSetting::first();
            AuditLog::record('commission_settings_updated', $firstSetting, ['changes' => $changes], ['changes' => $changes]);
            $this->dispatch('toast', type: 'success', message: 'Commission settings saved.');
        } else {
            $this->dispatch('toast', type: 'info', message: 'No changes to save.');
        }
    }

    protected function validationRules(): array
    {
        return [
            'settings.min_gm_percent.value' => 'required|numeric|min:0|max:100',
            'settings.target_gm_percent.value' => 'required|numeric|min:0|max:100',
            'settings.fast_close_spiff.value' => 'required|numeric|min:0',
            'settings.fast_close_days.value' => 'required|numeric|min:1|max:30',
            'settings.floor_min_amount.value' => 'required|numeric|min:0',
            'settings.floor_percent.value' => 'required|numeric|min:0|max:100',
            'settings.tier_35_1_37_9_rate.value' => 'required|numeric|min:0|max:100',
            'settings.tier_38_40_9_rate.value' => 'required|numeric|min:0|max:100',
            'settings.tier_41_43_9_rate.value' => 'required|numeric|min:0|max:100',
            'settings.tier_44_46_9_rate.value' => 'required|numeric|min:0|max:100',
            'settings.tier_47_rate.value' => 'required|numeric|min:0|max:100',
            'settings.surplus_multiplier.value' => 'required|numeric|min:0|max:10',
        ];
    }

    public function render()
    {
        return view('livewire.admin.commission-settings')
            ->layout('layouts.app')
            ->title('Commission Settings');
    }
}
