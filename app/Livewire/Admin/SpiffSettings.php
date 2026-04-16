<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class SpiffSettings extends Component
{
    public function render()
    {
        return view('livewire.admin.spiff-settings')
            ->layout('layouts.app')
            ->title('SPIFF Settings');
    }
}
