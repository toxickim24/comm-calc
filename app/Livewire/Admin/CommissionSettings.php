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
                'value' => $s->value,
                'label' => $s->label,
                'description' => $s->description,
            ])
            ->toArray();
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
