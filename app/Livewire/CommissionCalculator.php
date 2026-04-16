<?php

namespace App\Livewire;

use App\Services\CommissionCalculatorService;
use Livewire\Component;

class CommissionCalculator extends Component
{
    public string $contract_value = '';
    public string $gm_percent = '';
    public bool $is_fast_close = false;

    public ?array $result = null;

    public function updated($property): void
    {
        if (in_array($property, ['contract_value', 'gm_percent', 'is_fast_close'])) {
            $this->calculateCommission();
        }
    }

    public function calculateCommission(): void
    {
        $contractValue = (float) $this->contract_value;
        $gmPercent = (float) $this->gm_percent;

        if ($contractValue <= 0 || $gmPercent <= 0) {
            $this->result = null;
            return;
        }

        $service = new CommissionCalculatorService();
        $this->result = $service->calculate($contractValue, $gmPercent, $this->is_fast_close);
    }

    public function clear(): void
    {
        $this->contract_value = '';
        $this->gm_percent = '';
        $this->is_fast_close = false;
        $this->result = null;
    }

    public function render()
    {
        $service = new CommissionCalculatorService();

        return view('livewire.commission-calculator', [
            'tiers' => $service->getTiers(),
        ])
            ->layout('layouts.app')
            ->title('Commission Calculator');
    }
}
