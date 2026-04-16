<div class="mx-auto max-w-lg">
    @if($isForced)
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <p class="text-sm font-medium text-amber-800">Your administrator requires you to change your password before continuing.</p>
            </div>
        </div>
    @endif

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <h2 class="mb-6 text-lg font-semibold text-gray-900">Change Password</h2>

        <form wire:submit="changePassword" class="space-y-5">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input wire:model="current_password"
                       type="password"
                       id="current_password"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input wire:model="password"
                       type="password"
                       id="password"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input wire:model="password_confirmation"
                       type="password"
                       id="password_confirmation"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50">
                    Update Password
                </button>
                @if(!$isForced)
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                @endif
            </div>
        </form>
    </div>
</div>
