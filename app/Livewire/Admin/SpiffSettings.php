<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\SpiffSetting;
use Livewire\Component;

class SpiffSettings extends Component
{
    public array $settings = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $this->settings = SpiffSetting::all()
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
        // All SPIFF monetary and count values are whole numbers
        if ($value == (int) $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 4), '0'), '.');
    }

    public function save(): void
    {
        $this->validate($this->validationRules());

        $changes = [];

        foreach ($this->settings as $key => $data) {
            $setting = SpiffSetting::find($data['id']);
            if (!$setting) continue;

            $oldValue = $setting->value;
            $newValue = (float) $data['value'];

            if ((float) $oldValue !== $newValue) {
                $changes[$key] = ['from' => $oldValue, 'to' => $newValue];
                $setting->update(['value' => $newValue]);
            }
        }

        if (!empty($changes)) {
            $firstSetting = SpiffSetting::first();
            AuditLog::record('spiff_settings_updated', $firstSetting, ['changes' => $changes], ['changes' => $changes]);
            $this->dispatch('toast', type: 'success', message: 'SPIFF settings saved.');
        } else {
            $this->dispatch('toast', type: 'info', message: 'No changes to save.');
        }
    }

    protected function validationRules(): array
    {
        return [
            'settings.improvement_bonus.value' => 'required|numeric|min:0',
            'settings.improvement_min_appts.value' => 'required|numeric|min:1',
            'settings.improvement_min_points.value' => 'required|numeric|min:1|max:100',
            'settings.target_20_bonus.value' => 'required|numeric|min:0',
            'settings.target_30_bonus.value' => 'required|numeric|min:0',
            'settings.target_min_appts.value' => 'required|numeric|min:1',
            'settings.fast_close_per_deal.value' => 'required|numeric|min:0',
            'settings.highest_close_rate_bonus.value' => 'required|numeric|min:0',
            'settings.tie_handling.value' => 'required|numeric|in:1,2,3',
        ];
    }

    public function render()
    {
        return view('livewire.admin.spiff-settings')
            ->layout('layouts.app')
            ->title('SPIFF Settings');
    }
}
