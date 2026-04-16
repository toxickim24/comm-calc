<?php

namespace App\Livewire;

use Livewire\Component;

class DealLog extends Component
{
    public function render()
    {
        return view('livewire.deal-log')
            ->layout('layouts.app')
            ->title('Deal Log');
    }
}
