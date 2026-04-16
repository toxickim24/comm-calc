<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $stats = [
            'total_users' => $user->isAdmin() ? User::count() : null,
            'total_deals' => $user->isSalesRep()
                ? Deal::where('user_id', $user->id)->count()
                : Deal::count(),
            'active_reps' => $user->isAdmin() || $user->isManager()
                ? User::where('role', UserRole::SalesRep)->where('is_active', true)->count()
                : null,
        ];

        return view('livewire.dashboard', compact('stats'))
            ->layout('layouts.app')
            ->title('Dashboard');
    }
}
