<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Month-End Locking</h2>
        <p class="text-sm text-gray-500">Freeze monthly commission and SPIFF settings for auditability</p>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Month</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Details</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($months as $m)
                <tr class="transition hover:bg-gray-50" wire:key="month-{{ $m['month'] }}">
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-900">{{ $m['label'] }}</span>
                    </td>

                    <td class="px-6 py-4">
                        @if($m['is_locked'])
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                Locked
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"/></svg>
                                Open
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-xs text-gray-500">
                        @if($m['is_locked'] && $m['locked_at'])
                            Locked by {{ $m['locked_by'] ?? 'Unknown' }} on {{ $m['locked_at']->format('M d, Y g:i A') }}
                        @elseif(!$m['is_locked'] && $m['unlocked_at'])
                            Unlocked by {{ $m['unlocked_by'] ?? 'Unknown' }} on {{ $m['unlocked_at']->format('M d, Y g:i A') }}
                        @else
                            Never locked
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        @if($m['is_locked'])
                            <button x-on:click="confirmAction({ title: 'Unlock {{ $m['label'] }}?', text: 'This will allow changes to deals and payouts for this month.' }).then(result => { if(result.isConfirmed) $wire.unlock('{{ $m['month'] }}') })"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700 transition hover:bg-amber-100">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                Unlock
                            </button>
                        @else
                            <button wire:click="prepLock('{{ $m['month'] }}')"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Lock Month
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-700">
        <strong>What does locking do?</strong> When a month is locked, a snapshot of the current commission and SPIFF settings is saved. This ensures historical payouts can be audited against the exact settings that were in effect at the time.
    </div>

    {{-- Pre-Lock Checklist Modal --}}
    @if($showChecklist)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50" x-transition>
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.set('showChecklist', false)">
            <h3 class="mb-1 text-lg font-semibold text-gray-900">Lock {{ $checklist['month_label'] }}</h3>
            <p class="mb-5 text-sm text-gray-500">Review the following before locking this month.</p>

            <div class="space-y-3">
                {{-- Total Deals --}}
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                    <span class="text-sm text-gray-700">Total deals</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $checklist['total_deals'] }}</span>
                </div>

                {{-- Open Deals --}}
                <div class="flex items-center justify-between rounded-lg {{ $checklist['open_deals'] > 0 ? 'bg-amber-50' : 'bg-green-50' }} px-4 py-3">
                    <span class="text-sm {{ $checklist['open_deals'] > 0 ? 'text-amber-700' : 'text-green-700' }}">
                        {{ $checklist['open_deals'] > 0 ? 'Open deals (not closed)' : 'All deals are closed' }}
                    </span>
                    <span class="text-sm font-semibold {{ $checklist['open_deals'] > 0 ? 'text-amber-700' : 'text-green-700' }}">
                        {{ $checklist['open_deals'] > 0 ? $checklist['open_deals'] . ' remaining' : 'OK' }}
                    </span>
                </div>

                {{-- Missing Appointment Dates --}}
                <div class="flex items-center justify-between rounded-lg {{ $checklist['missing_appt_date'] > 0 ? 'bg-amber-50' : 'bg-green-50' }} px-4 py-3">
                    <span class="text-sm {{ $checklist['missing_appt_date'] > 0 ? 'text-amber-700' : 'text-green-700' }}">
                        {{ $checklist['missing_appt_date'] > 0 ? 'Deals missing appointment date' : 'All appointment dates present' }}
                    </span>
                    <span class="text-sm font-semibold {{ $checklist['missing_appt_date'] > 0 ? 'text-amber-700' : 'text-green-700' }}">
                        {{ $checklist['missing_appt_date'] > 0 ? $checklist['missing_appt_date'] . ' missing' : 'OK' }}
                    </span>
                </div>

                {{-- CW Missing Signed Dates --}}
                <div class="flex items-center justify-between rounded-lg {{ $checklist['closed_won_missing_signed'] > 0 ? 'bg-red-50' : 'bg-green-50' }} px-4 py-3">
                    <span class="text-sm {{ $checklist['closed_won_missing_signed'] > 0 ? 'text-red-700' : 'text-green-700' }}">
                        {{ $checklist['closed_won_missing_signed'] > 0 ? 'Closed Won deals missing signed date' : 'All signed dates present' }}
                    </span>
                    <span class="text-sm font-semibold {{ $checklist['closed_won_missing_signed'] > 0 ? 'text-red-700' : 'text-green-700' }}">
                        {{ $checklist['closed_won_missing_signed'] > 0 ? $checklist['closed_won_missing_signed'] . ' missing' : 'OK' }}
                    </span>
                </div>

                {{-- SPIFF Status --}}
                <div class="flex items-center justify-between rounded-lg {{ $checklist['spiff_not_calculated'] ? 'bg-amber-50' : 'bg-green-50' }} px-4 py-3">
                    <span class="text-sm {{ $checklist['spiff_not_calculated'] ? 'text-amber-700' : 'text-green-700' }}">
                        {{ $checklist['spiff_not_calculated'] ? 'SPIFFs not calculated for all reps' : 'SPIFFs calculated' }}
                    </span>
                    <span class="text-sm font-semibold {{ $checklist['spiff_not_calculated'] ? 'text-amber-700' : 'text-green-700' }}">
                        {{ $checklist['spiff_not_calculated'] ? $checklist['spiff_count'] . '/' . $checklist['rep_count'] . ' reps' : 'OK' }}
                    </span>
                </div>
            </div>

            @if(!$checklist['all_clear'])
            <div class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-700">
                There are unresolved items above. You can still lock, but data for this month may be incomplete.
            </div>
            @endif

            <div class="mt-5 flex items-center justify-end gap-3">
                <button wire:click="$set('showChecklist', false)"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button wire:click="confirmLock"
                        class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    {{ $checklist['all_clear'] ? 'Lock Month' : 'Lock Anyway' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
