<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\PasswordChangeLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'sales_rep';
    public bool $is_active = true;
    public bool $force_password_change = false;

    public string $search = '';
    public string $filterRole = '';

    public bool $showResetModal = false;
    public ?int $resetUserId = null;
    public string $resetUserName = '';
    public string $newPassword = '';
    public bool $forceChangeAfterReset = true;

    protected function rules(): array
    {
        $emailRule = $this->editing
            ? "required|email|unique:users,email,{$this->editingId}"
            : 'required|email|unique:users,email';

        $rules = [
            'name' => 'required|min:2|max:255',
            'email' => $emailRule,
            'role' => 'required|in:admin,manager,sales_rep',
            'is_active' => 'boolean',
            'force_password_change' => 'boolean',
        ];

        if (!$this->editing) {
            $rules['password'] = 'required|min:8';
        }

        return $rules;
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->is_active = $user->is_active;
        $this->force_password_change = $user->force_password_change;
        $this->password = '';
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editing) {
            $user = User::findOrFail($this->editingId);
            $oldValues = $user->only(['name', 'email', 'role', 'is_active']);

            // Prevent removing last admin
            if ($user->isAdmin() && $this->role !== 'admin') {
                if (User::where('role', 'admin')->where('id', '!=', $user->id)->count() === 0) {
                    $this->dispatch('toast', type: 'error', message: 'Cannot change role. At least one admin must exist.');
                    return;
                }
            }

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'force_password_change' => $this->force_password_change,
            ];

            $user->update($data);

            AuditLog::record('user_updated', $user, $oldValues, $data);

            $this->dispatch('toast', type: 'success', message: 'User updated successfully.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'force_password_change' => $this->force_password_change,
            ]);

            AuditLog::record('user_created', $user, null, $user->toArray());

            $this->dispatch('toast', type: 'success', message: 'User created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'You cannot delete your own account.');
            return;
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            $this->dispatch('toast', type: 'error', message: 'Cannot delete the last admin user.');
            return;
        }

        AuditLog::record('user_deleted', $user, $user->toArray());
        $user->delete();

        $this->dispatch('toast', type: 'success', message: 'User deleted successfully.');
    }

    public function openResetPassword(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetUserId = $user->id;
        $this->resetUserName = $user->name;
        $this->newPassword = '';
        $this->forceChangeAfterReset = true;
        $this->showResetModal = true;
    }

    public function resetPassword(): void
    {
        $this->validate([
            'newPassword' => 'required|min:8',
        ]);

        $user = User::findOrFail($this->resetUserId);
        $user->update([
            'password' => Hash::make($this->newPassword),
            'force_password_change' => $this->forceChangeAfterReset,
        ]);

        PasswordChangeLog::create([
            'user_id' => $user->id,
            'changed_by' => auth()->id(),
            'change_type' => 'admin_reset',
        ]);

        AuditLog::record('password_reset', $user);

        $this->showResetModal = false;
        $this->dispatch('toast', type: 'success', message: "Password reset for {$user->name}.");
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'editingId', 'editing']);
        $this->role = 'sales_rep';
        $this->is_active = true;
        $this->force_password_change = false;
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where(fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ])->layout('layouts.app')->title('User Management');
    }
}
