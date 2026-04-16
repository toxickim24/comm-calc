<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Monthly SPIFF</h2>
            <p class="text-sm text-gray-500">Monthly bonus calculations and payouts</p>
        </div>
        <button wire:click="calculate"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-50">
            <svg wire:loading.remove wire:target="calculate" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <svg wire:loading wire:target="calculate" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Calculate SPIFFs
        </button>
    </div>

    {{-- Month Navigator --}}
    <div class="mb-6 flex items-center justify-between rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <button wire:click="previousMonth"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <div class="text-center">
            <p class="text-lg font-semibold text-gray-900">{{ $monthDate->format('F Y') }}</p>
            @if($isCurrentMonth)
                <p class="text-xs text-brand-600">
                    <span class="inline-flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Current Month
                    </span>
                </p>
            @endif
        </div>

        <div class="flex items-center gap-1">
            @if(!$isCurrentMonth)
            <button wire:click="currentMonth"
                    class="rounded-lg px-3 py-2 text-xs font-medium text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
                Today
            </button>
            @endif
            <button wire:click="nextMonth"
                    @if($isCurrentMonth) disabled @endif
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-30">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- Grand Total --}}
    @if($payouts->isNotEmpty())
    <div class="mb-6 rounded-xl bg-brand-600 p-5 text-center text-white shadow-sm">
        <p class="text-sm font-medium text-brand-100">Total SPIFF Payouts — {{ $monthDate->format('F Y') }}</p>
        <p class="mt-1 text-3xl font-bold">${{ number_format($grandTotal, 2) }}</p>
        <p class="mt-1 text-sm text-brand-200">{{ $payouts->count() }} {{ Str::plural('rep', $payouts->count()) }}</p>
    </div>
    @endif

    {{-- Payouts Table --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rep</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Appts</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Closed</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Close %</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Improve</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Target</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Fast</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Top Rate</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payouts as $payout)
                    <tr class="transition hover:bg-gray-50" wire:key="payout-{{ $payout->id }}">
                        {{-- Rep --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900">{{ $payout->user->name }}</span>
                                @if($payout->is_override)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
                                          data-tippy-content="{{ $payout->override_notes }}">
                                        Override
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Appointments --}}
                        <td class="px-4 py-3 text-right text-sm text-gray-700">{{ $payout->appointments }}</td>

                        {{-- Deals Closed --}}
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ $payout->deals_closed }}</td>

                        {{-- Close Rate --}}
                        <td class="px-4 py-3 text-right text-sm">
                            <span class="font-medium {{ $payout->close_rate >= 30 ? 'text-green-600' : ($payout->close_rate >= 20 ? 'text-brand-600' : 'text-gray-700') }}">
                                {{ number_format($payout->close_rate, 1) }}%
                            </span>
                            @if($payout->improvement_points > 0)
                                <span class="ml-1 text-xs text-green-500">+{{ number_format($payout->improvement_points, 1) }}</span>
                            @elseif($payout->improvement_points < 0)
                                <span class="ml-1 text-xs text-red-400">{{ number_format($payout->improvement_points, 1) }}</span>
                            @endif
                        </td>

                        {{-- Improvement Bonus --}}
                        <td class="px-4 py-3 text-right text-sm {{ $payout->improvement_bonus > 0 ? 'font-medium text-green-600' : 'text-gray-400' }}">
                            ${{ number_format($payout->improvement_bonus, 0) }}
                        </td>

                        {{-- Target Bonus --}}
                        <td class="px-4 py-3 text-right text-sm {{ $payout->target_bonus > 0 ? 'font-medium text-green-600' : 'text-gray-400' }}">
                            ${{ number_format($payout->target_bonus, 0) }}
                        </td>

                        {{-- Fast Close Bonus --}}
                        <td class="px-4 py-3 text-right text-sm {{ $payout->fast_close_bonus > 0 ? 'font-medium text-amber-600' : 'text-gray-400' }}">
                            @if($payout->fast_close_count > 0)
                                ${{ number_format($payout->fast_close_bonus, 0) }}
                                <span class="text-xs text-gray-400">({{ $payout->fast_close_count }})</span>
                            @else
                                $0
                            @endif
                        </td>

                        {{-- Highest Close Rate Bonus --}}
                        <td class="px-4 py-3 text-right text-sm {{ $payout->highest_close_rate_bonus > 0 ? 'font-medium text-purple-600' : 'text-gray-400' }}">
                            ${{ number_format($payout->highest_close_rate_bonus, 0) }}
                        </td>

                        {{-- Total --}}
                        <td class="px-4 py-3 text-right">
                            <span class="text-sm font-bold {{ $payout->total_spiff > 0 ? 'text-brand-600' : 'text-gray-400' }}">
                                ${{ number_format($payout->total_spiff, 2) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openOverride({{ $payout->id }})"
                                        class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-brand-600"
                                        data-tippy-content="Manual override">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if($payout->is_override)
                                <button x-on:click="confirmAction({ title: 'Clear Override?', text: 'This will recalculate the SPIFF from deal data.' }).then(result => { if(result.isConfirmed) $wire.clearOverride({{ $payout->id }}) })"
                                        class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-amber-600"
                                        data-tippy-content="Clear override">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">
                            No SPIFF payouts for this month. Click "Calculate SPIFFs" to generate from deal data.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Override Modal --}}
    @if($showOverrideModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50" x-transition>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.set('showOverrideModal', false)">
            <h3 class="mb-1 text-lg font-semibold text-gray-900">Override SPIFF Payout</h3>
            <p class="mb-4 text-sm text-gray-500">Manually adjust total SPIFF for <strong>{{ $overrideRepName }}</strong></p>

            <form wire:submit="saveOverride" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total SPIFF Amount</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                        <input wire:model="overrideTotal" type="number" step="0.01" min="0"
                               class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                    </div>
                    @error('overrideTotal') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Override Notes <span class="text-red-500">*</span></label>
                    <textarea wire:model="overrideNotes" rows="3" placeholder="Explain why this override is being applied..."
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"></textarea>
                    @error('overrideNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showOverrideModal', false)"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        Save Override
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
