<?php

namespace App\Livewire;

use Livewire\Component;

class CommissionCalculator extends Component
{
    public function render()
    {
        return view('livewire.commission-calculator')
            ->layout('layouts.app')
            ->title('Commission Calculator');
    }
}
