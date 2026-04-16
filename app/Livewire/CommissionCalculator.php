<?php

namespace App\Livewire;

use App\Services\CommissionCalculatorService;
use Livewire\Component;

class CommissionCalculator extends Component
{
    public string $contract_value = '';
    public string $gm_percent = '';
    public ?array $result = null;

    // Comparison scenario B
    public bool $compareMode = false;
    public string $contract_value_b = '';
    public string $gm_percent_b = '';
    public ?array $result_b = null;

    public function updated($property): void
    {
        if (in_array($property, ['contract_value', 'gm_percent'])) {
            $this->calculateCommission();
            if ($this->compareMode && !$this->contract_value_b) {
                $this->contract_value_b = $this->contract_value;
            }
        }
        if (in_array($property, ['contract_value_b', 'gm_percent_b'])) {
            $this->calculateCompare();
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
        $this->result = $service->calculate($contractValue, $gmPercent, false);
    }

    public function calculateCompare(): void
    {
        $contractValue = (float) ($this->contract_value_b ?: $this->contract_value);
        $gmPercent = (float) $this->gm_percent_b;

        if ($contractValue <= 0 || $gmPercent <= 0) {
            $this->result_b = null;
            return;
        }

        $service = new CommissionCalculatorService();
        $this->result_b = $service->calculate($contractValue, $gmPercent, false);
    }

    public function toggleCompare(): void
    {
        $this->compareMode = !$this->compareMode;
        if ($this->compareMode) {
            $this->contract_value_b = $this->contract_value;
            $this->gm_percent_b = '';
            $this->result_b = null;
        } else {
            $this->result_b = null;
        }
    }

    public function clear(): void
    {
        $this->contract_value = '';
        $this->gm_percent = '';
        $this->result = null;
        $this->contract_value_b = '';
        $this->gm_percent_b = '';
        $this->result_b = null;
        $this->compareMode = false;
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
