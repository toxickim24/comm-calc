<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">SPIFF Settings</h2>
        <p class="text-sm text-gray-500">Configure monthly SPIFF bonuses and thresholds</p>
    </div>

    {{-- Current SPIFF Summary --}}
    <div class="mb-6 rounded-xl bg-brand-50 p-5 ring-1 ring-brand-200">
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wider text-brand-700">Current SPIFF Schedule</h3>
        <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
            <div class="flex justify-between">
                <span class="text-gray-500">Improvement Bonus</span>
                <span class="font-medium text-gray-900">${{ number_format((float)$settings['improvement_bonus']['value'], 0) }} ({{ (int)$settings['improvement_min_points']['value'] }}+ pp, {{ (int)$settings['improvement_min_appts']['value'] }}+ appts)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">20% Target Bonus</span>
                <span class="font-medium text-gray-900">${{ number_format((float)$settings['target_20_bonus']['value'], 0) }} ({{ (int)$settings['target_min_appts']['value'] }}+ appts)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">30%+ Target Bonus</span>
                <span class="font-medium text-gray-900">${{ number_format((float)$settings['target_30_bonus']['value'], 0, ',', ',') }} ({{ (int)$settings['target_min_appts']['value'] }}+ appts)</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Fast Close per Deal</span>
                <span class="font-medium text-gray-900">${{ number_format((float)$settings['fast_close_per_deal']['value'], 0) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Highest Close Rate</span>
                <span class="font-medium text-gray-900">${{ number_format((float)$settings['highest_close_rate_bonus']['value'], 0) }}</span>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Improvement Bonus --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-1 text-sm font-semibold uppercase tracking-wider text-gray-500">Close Rate Improvement Bonus</h3>
            <p class="mb-4 text-xs text-gray-500">Awarded when a rep improves their close rate by the minimum points with enough appointments.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bonus Amount</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.improvement_bonus.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.improvement_bonus.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Improvement</label>
                    <div class="relative mt-1">
                        <input wire:model="settings.improvement_min_points.value"
                               type="number" step="1" min="1"
                               class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-16 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">points</span>
                    </div>
                    @error('settings.improvement_min_points.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Appointments</label>
                    <input wire:model="settings.improvement_min_appts.value"
                           type="number" step="1" min="1"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    @error('settings.improvement_min_appts.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Target Close Rate Bonuses --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-1 text-sm font-semibold uppercase tracking-wider text-gray-500">Target Close Rate Bonuses</h3>
            <p class="mb-4 text-xs text-gray-500">Both tiers require the minimum number of appointments. The 30%+ bonus replaces the 20% bonus (not stacked).</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">20% Close Rate Bonus</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.target_20_bonus.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.target_20_bonus.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">30%+ Close Rate Bonus</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.target_30_bonus.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.target_30_bonus.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Appointments</label>
                    <input wire:model="settings.target_min_appts.value"
                           type="number" step="1" min="1"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Required for both 20% and 30%+ bonuses</p>
                    @error('settings.target_min_appts.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Fast Close & Highest Rate --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-1 text-sm font-semibold uppercase tracking-wider text-gray-500">Fast Close & Highest Close Rate</h3>
            <p class="mb-4 text-xs text-gray-500">Fast close is auto-determined from deal dates. Highest close rate is awarded monthly to the top-performing rep.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fast Close per Deal</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.fast_close_per_deal.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.fast_close_per_deal.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Highest Close Rate Bonus</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="settings.highest_close_rate_bonus.value"
                               type="number" step="1" min="0"
                               class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('settings.highest_close_rate_bonus.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tie Handling</label>
                    <select wire:model="settings.tie_handling.value"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        <option value="1">All tied reps get full bonus</option>
                        <option value="2">Split bonus among tied reps</option>
                        <option value="3">No bonus on tie</option>
                    </select>
                    @error('settings.tie_handling.value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
