<?php

namespace App\Livewire;

use Livewire\Component;

class MonthlySpiff extends Component
{
    public function render()
    {
        return view('livewire.monthly-spiff')
            ->layout('layouts.app')
            ->title('Monthly SPIFF');
    }
}
