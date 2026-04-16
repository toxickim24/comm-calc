<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Models\PasswordChangeLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $isForced = false;

    public function mount(): void
    {
        $this->isForced = Auth::user()->force_password_change;
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|different:current_password',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->update([
            'password' => $this->password,
            'force_password_change' => false,
        ]);

        PasswordChangeLog::create([
            'user_id' => $user->id,
            'changed_by' => $user->id,
            'change_type' => 'self',
        ]);

        AuditLog::record('password_changed', $user);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('toast', type: 'success', message: 'Password changed successfully.');

        if ($this->isForced) {
            $this->redirect(route('dashboard'));
        }
    }

    public function render()
    {
        return view('livewire.auth.change-password')
            ->layout('layouts.app')
            ->title('Change Password');
    }
}
