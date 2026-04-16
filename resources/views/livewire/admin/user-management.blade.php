<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">User Management</h2>
            <p class="text-sm text-gray-500">Manage user accounts and roles</p>
        </div>
        <button wire:click="create"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </button>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-col gap-3 sm:flex-row">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Search users..."
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
        </div>
        <select wire:model.live="filterRole"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
            <option value="">All Roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->value }}">{{ $role->label() }}</option>
            @endforeach
        </select>
    </div>

    <!-- Users Table -->
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="transition hover:bg-gray-50" wire:key="user-{{ $user->id }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-medium text-brand-700">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $roleColors = ['admin' => 'red', 'manager' => 'blue', 'sales_rep' => 'green'];
                            $c = $roleColors[$user->role->value] ?? 'gray';
                        @endphp
                        <span class="inline-flex rounded-full bg-{{ $c }}-100 px-2.5 py-0.5 text-xs font-medium text-{{ $c }}-800">
                            {{ $user->role->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Inactive
                            </span>
                        @endif
                        @if($user->force_password_change)
                            <span class="ml-2 text-xs text-amber-600" data-tippy-content="Must change password on next login">PW Reset</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="edit({{ $user->id }})"
                                    class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-brand-600"
                                    data-tippy-content="Edit user">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="openResetPassword({{ $user->id }})"
                                    class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-amber-600"
                                    data-tippy-content="Reset password">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </button>
                            @if($user->id !== auth()->id())
                            <button x-on:click="confirmAction({ title: 'Delete User?', text: 'This will deactivate {{ addslashes($user->name) }}\'s account.' }).then(result => { if(result.isConfirmed) $wire.delete({{ $user->id }}) })"
                                    class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-red-600"
                                    data-tippy-content="Delete user">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="border-t border-gray-200 px-6 py-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50" x-transition>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.set('showModal', false)">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">{{ $editing ? 'Edit User' : 'Create User' }}</h3>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input wire:model="name" type="text" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input wire:model="email" type="email" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                @if(!$editing)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input wire:model="password" type="password" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select wire:model="role" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        @foreach(\App\Enums\UserRole::cases() as $r)
                            <option value="{{ $r->value }}">{{ $r->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input wire:model="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input wire:model="force_password_change" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-gray-700">Force password change</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        {{ $editing ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Reset Password Modal -->
    @if($showResetModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50" x-transition>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.set('showResetModal', false)">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">Reset Password</h3>
            <p class="mb-4 text-sm text-gray-600">Set a new password for <strong>{{ $resetUserName }}</strong></p>

            <form wire:submit="resetPassword" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input wire:model="newPassword" type="password" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    @error('newPassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2">
                    <input wire:model="forceChangeAfterReset" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-gray-700">Require password change on next login</span>
                </label>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showResetModal', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
