<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Commission Settings</h2>
        <p class="text-sm text-gray-500">Configure commission tiers, rates, and thresholds</p>
    </div>

    {{-- Current Tier Summary --}}
    <div class="mb-6 rounded-xl bg-brand-50 p-5 ring-1 ring-brand-200">
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-brand-700">Current Commission Schedule</h3>
        <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm sm:grid-cols-4">
            <div class="text-gray-500">Below {{ rtrim(rtrim(number_format((float)$settings['min_gm_percent']['value'], 1), '0'), '.') }}%</div>
            <div class="font-medium text-gray-900">No commission</div>
            <div class="text-gray-500">Exactly {{ rtrim(rtrim(number_format((float)$settings['min_gm_percent']['value'], 1), '0'), '.') }}%</div>
            <div class="font-medium text-gray-900">MAX(${{ number_format((float)$settings['floor_min_amount']['value'], 0) }}, {{ rtrim(rtrim(number_format((float)$settings['floor_percent']['value'], 1), '0'), '.') }}%)</div>
            <div class="text-gray-500">35.1% – 37.9%</div>
            <div class="font-medium text-gray-900">{{ rtrim(rtrim(number_format((float)$settings['tier_35_1_37_9_rate']['value'], 1), '0'), '.') }}%</div>
            <div class="text-gray-500">38% – 40.9%</div>
            <div class="font-medium text-gray-900">{{ rtrim(rtrim(number_format((float)$settings['tier_38_40_9_rate']['value'], 1), '0'), '.') }}%</div>
            <div class="text-gray-500">41% – 43.9%</div>
            <div class="font-medium text-gray-900">{{ rtrim(rtrim(number_format((float)$settings['tier_41_43_9_rate']['value'], 1), '0'), '.') }}%</div>
            <div class="text-gray-500">44% – 46.9%</div>
            <div class="font-medium text-gray-900">{{ rtrim(rtrim(number_format((float)$settings['tier_44_46_9_rate']['value'], 1), '0'), '.') }}%</div>
            <div class="text-gray-500">47%+</div>
            <div class="font-medium text-gray-900">{{ rtrim(rtrim(number_format((float)$settings['tier_47_rate']['value'], 1), '0'), '.') }}% + surplus</div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Thresholds --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Gross Margin Thresholds</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum GM %</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.min_gm_percent.value"
                               type="number" step="1" min="0" max="100"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Below this = no commission</p>
                    @error('settings.min_gm_percent.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target GM %</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.target_gm_percent.value"
                               type="number" step="1" min="0" max="100"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Above this = surplus bonus applies</p>
                    @error('settings.target_gm_percent.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Floor Protection --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Floor Protection (at Minimum GM%)</h3>
            <p class="mb-4 text-xs text-gray-500">At exactly the minimum GM%, the rep earns the greater of the flat minimum or the floor percentage of the contract value.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Floor Minimum Amount</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.floor_min_amount.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pl-7 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.floor_min_amount.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Floor Rate</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.floor_percent.value"
                               type="number" step="0.1" min="0" max="100"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                    </div>
                    @error('settings.floor_percent.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Commission Tier Rates --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Commission Tier Rates</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $tierLabels = [
                        'tier_35_1_37_9_rate' => '35.1% – 37.9% GM',
                        'tier_38_40_9_rate' => '38% – 40.9% GM',
                        'tier_41_43_9_rate' => '41% – 43.9% GM',
                        'tier_44_46_9_rate' => '44% – 46.9% GM',
                        'tier_47_rate' => '47%+ GM',
                    ];
                @endphp
                @foreach($tierLabels as $key => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.{{ $key }}.value"
                               type="number" step="1" min="0" max="100"
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
                    <label class="block text-sm font-medium text-gray-700">Surplus Multiplier</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.surplus_multiplier.value"
                               type="number" step="0.1" min="0" max="10"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">x</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Applied to GM above target</p>
                    @error('settings.surplus_multiplier.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fast Close Bonus</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.fast_close_spiff.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pl-7 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Per qualifying deal</p>
                    @error('settings.fast_close_spiff.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fast Close Threshold</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.fast_close_days.value"
                               type="number" step="1" min="1" max="30"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-12 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">days</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Max days to qualify</p>
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
