<div>
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input wire:model="email"
                   type="email"
                   id="email"
                   autofocus
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"
                   placeholder="you@company.com">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input wire:model="password"
                   type="password"
                   id="password"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"
                   placeholder="Enter your password">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2">
                <input wire:model="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <!-- Submit -->
        <button type="submit"
                wire:loading.attr="disabled"
                class="flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50">
            <span wire:loading.remove>Sign In</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Signing in...
            </span>
        </button>
    </form>
</div>
