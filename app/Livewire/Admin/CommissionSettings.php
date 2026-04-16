<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class CommissionSettings extends Component
{
    public function render()
    {
        return view('livewire.admin.commission-settings')
            ->layout('layouts.app')
            ->title('Commission Settings');
    }
}
