<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Commission Settings</h2>
        <p class="text-sm text-gray-500">Configure commission tiers, rates, and thresholds</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Thresholds --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Gross Margin Thresholds</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach(['min_gm_percent', 'target_gm_percent'] as $key)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings[$key]['label'] }}</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.{{ $key }}.value"
                               type="number" step="0.01"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $settings[$key]['description'] }}</p>
                    @error("settings.{$key}.value") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                @endforeach
            </div>
        </div>

        {{-- Floor Protection --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Floor Protection (at Minimum GM%)</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings['floor_min_amount']['label'] }}</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.floor_min_amount.value"
                               type="number" step="0.01"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pl-7 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $settings['floor_min_amount']['description'] }}</p>
                    @error('settings.floor_min_amount.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings['floor_percent']['label'] }}</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.floor_percent.value"
                               type="number" step="0.01"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $settings['floor_percent']['description'] }}</p>
                    @error('settings.floor_percent.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Commission Tier Rates --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Commission Tier Rates</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach(['tier_35_1_37_9_rate', 'tier_38_40_9_rate', 'tier_41_43_9_rate', 'tier_44_46_9_rate', 'tier_47_rate'] as $key)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings[$key]['label'] }}</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.{{ $key }}.value"
                               type="number" step="0.01"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                    @error("settings.{$key}.value") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                @endforeach
            </div>
        </div>

        {{-- Surplus & Fast Close --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Surplus & Fast Close Bonuses</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings['surplus_multiplier']['label'] }}</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.surplus_multiplier.value"
                               type="number" step="0.01"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">x</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $settings['surplus_multiplier']['description'] }}</p>
                    @error('settings.surplus_multiplier.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings['fast_close_spiff']['label'] }}</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.fast_close_spiff.value"
                               type="number" step="0.01"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pl-7 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.fast_close_spiff.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $settings['fast_close_days']['label'] }}</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.fast_close_days.value"
                               type="number" step="1"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-12 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">days</span>
                    </div>
                    @error('settings.fast_close_days.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
