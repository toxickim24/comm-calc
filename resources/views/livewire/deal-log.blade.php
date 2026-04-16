<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Deal Log</h2>
            <p class="text-sm text-gray-500">Manage your sales deals and pipeline</p>
        </div>
        <button wire:click="create"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Deal
        </button>
    </div>

    {{-- Filters --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Search by client name..."
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
        </div>
        <input wire:model.live="filterMonth"
               type="month"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
        <select wire:model.live="filterStatus"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
            @endforeach
        </select>
        @if($salesReps->isNotEmpty())
        <select wire:model.live="filterRep"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
            <option value="">All Reps</option>
            @foreach($salesReps as $rep)
                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
            @endforeach
        </select>
        @endif
    </div>

    {{-- Deals Table --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Client</th>
                        @if($salesReps->isNotEmpty())
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rep</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Contract</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">GM%</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Commission</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Days</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($deals as $deal)
                    <tr class="transition hover:bg-gray-50" wire:key="deal-{{ $deal->id }}">
                        {{-- Client --}}
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $deal->client_name }}</p>
                            <p class="text-xs text-gray-500">{{ $deal->month->format('M Y') }}</p>
                        </td>

                        {{-- Rep --}}
                        @if($salesReps->isNotEmpty())
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $deal->user->name }}</td>
                        @endif

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'gray' => 'bg-gray-100 text-gray-800',
                                    'blue' => 'bg-blue-100 text-blue-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'green' => 'bg-green-100 text-green-800',
                                    'red' => 'bg-red-100 text-red-800',
                                ];
                                $colorClass = $statusColors[$deal->deal_status->color()] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <select wire:change="updateStatus({{ $deal->id }}, $event.target.value)"
                                    class="rounded-full border-0 px-2.5 py-0.5 text-xs font-medium {{ $colorClass }} cursor-pointer focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected($deal->deal_status === $status)>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        {{-- Contract Value --}}
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">
                            ${{ number_format($deal->sold_contract_value, 2) }}
                        </td>

                        {{-- GM% --}}
                        <td class="px-4 py-3 text-right text-sm text-gray-700">
                            {{ number_format($deal->estimated_gm_percent, 1) }}%
                        </td>

                        {{-- Commission --}}
                        <td class="px-4 py-3 text-right text-sm">
                            @if($deal->commissionPayout)
                                <span class="font-semibold text-green-600">${{ number_format($deal->commissionPayout->total_payout, 2) }}</span>
                            @else
                                <span class="text-gray-400">--</span>
                            @endif
                        </td>

                        {{-- Days to Close --}}
                        <td class="px-4 py-3 text-center text-sm">
                            @if($deal->days_to_close !== null)
                                <span class="inline-flex items-center gap-1">
                                    {{ $deal->days_to_close }}
                                    @if($deal->is_fast_close)
                                        <svg class="h-3.5 w-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20" data-tippy-content="Fast Close">
                                            <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/>
                                        </svg>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400">--</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="edit({{ $deal->id }})"
                                        class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-brand-600"
                                        data-tippy-content="Edit deal">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button x-on:click="confirmAction({ title: 'Delete Deal?', text: 'This will remove the deal for {{ addslashes($deal->client_name) }}.' }).then(result => { if(result.isConfirmed) $wire.delete({{ $deal->id }}) })"
                                        class="rounded-md p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-red-600"
                                        data-tippy-content="Delete deal">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                            No deals found. Click "New Deal" to add one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deals->hasPages())
        <div class="border-t border-gray-200 px-6 py-3">
            {{ $deals->links() }}
        </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/50" x-transition>
        <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl" @click.outside="$wire.set('showModal', false)">
            <h3 class="mb-4 text-lg font-semibold text-gray-900">{{ $editing ? 'Edit Deal' : 'New Deal' }}</h3>

            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Client Name --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Client Name</label>
                        <input wire:model="client_name" type="text"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        @error('client_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Sales Rep (admin/manager only) --}}
                    @if($salesReps->isNotEmpty())
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sales Rep</label>
                        <select wire:model="user_id"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            <option value="">Select rep...</option>
                            @foreach($salesReps as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Month --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Month</label>
                        <input wire:model="month" type="month"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        @error('month') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model="deal_status"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('deal_status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Sold Contract Value --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sold Contract Value</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                            <input wire:model="sold_contract_value" type="number" step="0.01" min="0"
                                   class="block w-full rounded-lg border border-gray-300 py-2 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        </div>
                        @error('sold_contract_value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Original Contract Price --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Original Contract Price <span class="text-gray-400">(optional)</span></label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                            <input wire:model="original_contract_price" type="number" step="0.01" min="0"
                                   class="block w-full rounded-lg border border-gray-300 py-2 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        </div>
                        @error('original_contract_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Estimated GM% --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estimated Gross Margin</label>
                        <div class="relative mt-1">
                            <input wire:model="estimated_gm_percent" type="number" step="0.1" min="0" max="100"
                                   class="block w-full rounded-lg border border-gray-300 px-4 py-2 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                        </div>
                        @error('estimated_gm_percent') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Dates --}}
                <div>
                    <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">Dates</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Appointment</label>
                            <input wire:model="appointment_date" type="date"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            @error('appointment_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contract Signed</label>
                            <input wire:model="contract_signed_date" type="date"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            @error('contract_signed_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deposit</label>
                            <input wire:model="deposit_date" type="date"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            @error('deposit_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes <span class="text-gray-400">(optional)</span></label>
                    <textarea wire:model="notes" rows="2"
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"></textarea>
                    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">{{ $editing ? 'Update Deal' : 'Create Deal' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
