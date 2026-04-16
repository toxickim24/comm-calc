<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Commission Calculator</h2>
        <p class="text-sm text-gray-500">Calculate commission payouts based on deal details</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Input Panel --}}
        <div class="space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Deal Details</h3>
                <div class="space-y-4">
                    {{-- Contract Value --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sold Contract Value</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                            <input wire:model.live.debounce.300ms="contract_value"
                                   type="number" step="0.01" min="0"
                                   placeholder="e.g. 25000"
                                   class="block w-full rounded-lg border border-gray-300 py-2.5 pl-7 pr-4 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        </div>
                    </div>

                    {{-- GM Percent --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estimated Gross Margin</label>
                        <div class="relative mt-1">
                            <input wire:model.live.debounce.300ms="gm_percent"
                                   type="number" step="0.1" min="0" max="100"
                                   placeholder="e.g. 42"
                                   class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-8 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">%</span>
                        </div>
                    </div>

                    {{-- Fast Close Toggle --}}
                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-3">
                        <input wire:model.live="is_fast_close"
                               type="checkbox" id="fast_close"
                               class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <label for="fast_close" class="text-sm text-gray-700">
                            <span class="font-medium">Fast Close</span>
                            <span class="text-gray-500">— Deal closed within {{ \App\Models\CommissionSetting::getValue('fast_close_days', 3) }} days</span>
                        </label>
                    </div>

                    {{-- Clear Button --}}
                    <button wire:click="clear"
                            type="button"
                            class="text-sm font-medium text-gray-500 transition hover:text-gray-700">
                        Clear inputs
                    </button>
                </div>
            </div>

            {{-- Tier Reference Table --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Commission Tier Reference</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="pb-2 text-left font-medium text-gray-500">GM% Range</th>
                            <th class="pb-2 text-right font-medium text-gray-500">Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="text-gray-400">
                            <td class="py-2">Below 35%</td>
                            <td class="py-2 text-right">0%</td>
                        </tr>
                        @foreach($tiers as $tier)
                        <tr @if($result && $result['tier'] === $tier['label']) class="bg-brand-50 font-semibold text-brand-700" @else class="text-gray-700" @endif>
                            <td class="py-2">{{ $tier['label'] }}</td>
                            <td class="py-2 text-right">{{ number_format($tier['rate'], 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Results Panel --}}
        <div>
            @if($result)
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                {{-- Total Payout Header --}}
                <div class="rounded-t-xl bg-brand-600 px-6 py-5 text-center">
                    <p class="text-sm font-medium text-brand-100">Total Commission Payout</p>
                    <p class="mt-1 text-3xl font-bold text-white">${{ number_format($result['total_payout'], 2) }}</p>
                </div>

                {{-- Breakdown --}}
                <div class="p-6">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Breakdown</h3>
                    <dl class="space-y-3">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Contract Value</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($result['contract_value'], 2) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Gross Margin</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($result['gm_percent'], 1) }}%</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Tier</dt>
                            <dd class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                                {{ $result['tier'] }}
                                @if($result['tier_rate'] > 0)
                                    ({{ number_format($result['tier_rate'], 1) }}%)
                                @endif
                            </dd>
                        </div>

                        <div class="my-3 border-t border-gray-200"></div>

                        {{-- Base Commission --}}
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Base Commission</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($result['base_commission'], 2) }}</dd>
                        </div>

                        {{-- Surplus Bonus --}}
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">
                                Surplus Bonus
                                @if($result['surplus_bonus'] > 0)
                                    <span class="text-xs text-gray-400">({{ number_format($result['gm_percent'] - 47, 1) }}% above target)</span>
                                @endif
                            </dt>
                            <dd class="text-sm font-medium {{ $result['surplus_bonus'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                @if($result['surplus_bonus'] > 0)
                                    +${{ number_format($result['surplus_bonus'], 2) }}
                                @else
                                    $0.00
                                @endif
                            </dd>
                        </div>

                        {{-- Fast Close Bonus --}}
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Fast Close Bonus</dt>
                            <dd class="text-sm font-medium {{ $result['fast_close_bonus'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                @if($result['fast_close_bonus'] > 0)
                                    +${{ number_format($result['fast_close_bonus'], 2) }}
                                @else
                                    $0.00
                                @endif
                            </dd>
                        </div>

                        <div class="my-3 border-t border-gray-200"></div>

                        {{-- Total --}}
                        <div class="flex items-center justify-between">
                            <dt class="text-sm font-semibold text-gray-900">Total Payout</dt>
                            <dd class="text-lg font-bold text-brand-600">${{ number_format($result['total_payout'], 2) }}</dd>
                        </div>
                    </dl>

                    {{-- Warning for below floor --}}
                    @if($result['tier'] === 'Below Floor')
                    <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                        GM% is below the minimum threshold. No commission is earned on this deal.
                    </div>
                    @endif

                    {{-- Floor protection notice --}}
                    @if($result['gm_percent'] == 35 && $result['base_commission'] >= 750)
                    <div class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-700">
                        Floor protection applied: minimum $750 commission at 35% GM.
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <p class="mt-2 text-sm font-medium text-gray-500">Enter contract value and gross margin to see commission breakdown</p>
            </div>
            @endif
        </div>
    </div>
</div>
