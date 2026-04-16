<?php

namespace App\Livewire;

use Livewire\Component;

class WeeklyScoreboard extends Component
{
    public function render()
    {
        return view('livewire.weekly-scoreboard')
            ->layout('layouts.app')
            ->title('Weekly Scoreboard');
    }
}
