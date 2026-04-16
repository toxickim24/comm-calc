<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Weekly Scoreboard</h2>
            <p class="text-sm text-gray-500">Track weekly sales performance</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
        <button wire:click="recalculate"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-50">
            <svg wire:loading.remove wire:target="recalculate" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <svg wire:loading wire:target="recalculate" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Recalculate
        </button>
        @endif
    </div>

    {{-- Week Navigator --}}
    <div class="mb-6 flex items-center justify-between rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <button wire:click="previousWeek"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <div class="text-center">
            <p class="text-lg font-semibold text-gray-900">
                {{ $weekStartDate->format('M d') }} – {{ $weekEndDate->format('M d, Y') }}
            </p>
            <p class="text-xs text-gray-500">
                @if($isCurrentWeek)
                    <span class="inline-flex items-center gap-1 text-brand-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Current Week
                    </span>
                @else
                    Week {{ $weekStartDate->weekOfYear }}
                @endif
            </p>
        </div>

        <div class="flex items-center gap-1">
            @if(!$isCurrentWeek)
            <button wire:click="currentWeek"
                    class="rounded-lg px-3 py-2 text-xs font-medium text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
                Today
            </button>
            @endif
            <button wire:click="nextWeek"
                    @if($isCurrentWeek) disabled @endif
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 disabled:cursor-not-allowed disabled:opacity-30">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- Scoreboard Table --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if(!auth()->user()->isSalesRep())
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rep</th>
                        @endif

                        @php
                            $columns = [
                                'appointments' => 'Appts',
                                'quotes_sent' => 'Quotes',
                                'deals_closed' => 'Closed',
                                'close_rate' => 'Close %',
                                'avg_days_to_close' => 'Avg Days',
                                'fast_closes' => 'Fast',
                            ];
                        @endphp

                        @foreach($columns as $col => $label)
                        <th wire:click="sort('{{ $col }}')"
                            class="cursor-pointer px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 transition hover:text-brand-600">
                            <span class="inline-flex items-center gap-1">
                                {{ $label }}
                                @if($sortBy === $col)
                                    @if($sortDir === 'asc')
                                        <svg class="h-3 w-3 text-brand-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z"/></svg>
                                    @else
                                        <svg class="h-3 w-3 text-brand-600" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z"/></svg>
                                    @endif
                                @endif
                            </span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($scores as $index => $score)
                    <tr class="transition hover:bg-gray-50" wire:key="score-{{ $score->id }}">
                        @if(!auth()->user()->isSalesRep())
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                {{-- Rank badge --}}
                                @if($sortBy === 'deals_closed' && $sortDir === 'desc')
                                <div class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold
                                    {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-gray-200 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                                    {{ $index + 1 }}
                                </div>
                                @else
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-xs font-medium text-brand-700">
                                    {{ strtoupper(substr($score->user->name, 0, 1)) }}
                                </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $score->user->name }}</span>
                            </div>
                        </td>
                        @endif

                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ $score->appointments }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-700">{{ $score->quotes_sent }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-sm font-semibold {{ $score->deals_closed > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $score->deals_closed }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @php
                                $rateColor = $score->close_rate >= 30 ? 'text-green-600' : ($score->close_rate >= 20 ? 'text-brand-600' : ($score->close_rate > 0 ? 'text-gray-700' : 'text-gray-400'));
                            @endphp
                            <span class="text-sm font-medium {{ $rateColor }}">{{ number_format($score->close_rate, 1) }}%</span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-700">
                            {{ $score->avg_days_to_close > 0 ? number_format($score->avg_days_to_close, 1) : '--' }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm">
                            @if($score->fast_closes > 0)
                                <span class="inline-flex items-center gap-1 font-medium text-amber-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                                    {{ $score->fast_closes }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isSalesRep() ? 6 : 7 }}" class="px-6 py-8 text-center text-sm text-gray-500">
                            No scores for this week.
                            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                Click "Recalculate" to generate scores from deal data.
                            @endif
                        </td>
                    </tr>
                    @endforelse

                    {{-- Team Totals Row --}}
                    @if($teamTotals)
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-4 py-3 text-sm text-gray-700">Team Totals</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ $teamTotals['appointments'] }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ $teamTotals['quotes_sent'] }}</td>
                        <td class="px-4 py-3 text-right text-sm text-green-600">{{ $teamTotals['deals_closed'] }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($teamTotals['close_rate'], 1) }}%</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ $teamTotals['avg_days_to_close'] > 0 ? number_format($teamTotals['avg_days_to_close'], 1) : '--' }}</td>
                        <td class="px-4 py-3 text-right text-sm text-amber-600">{{ $teamTotals['fast_closes'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Personal Stats Cards (Sales Rep View) --}}
    @if(auth()->user()->isSalesRep() && $scores->isNotEmpty())
    @php $myScore = $scores->first(); @endphp
    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $myScore->appointments }}</p>
            <p class="text-xs font-medium text-gray-500">Appointments</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $myScore->quotes_sent }}</p>
            <p class="text-xs font-medium text-gray-500">Quotes Sent</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $myScore->deals_closed }}</p>
            <p class="text-xs font-medium text-gray-500">Deals Closed</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 text-center">
            <p class="text-2xl font-bold text-brand-600">{{ number_format($myScore->close_rate, 1) }}%</p>
            <p class="text-xs font-medium text-gray-500">Close Rate</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $myScore->avg_days_to_close > 0 ? number_format($myScore->avg_days_to_close, 1) : '--' }}</p>
            <p class="text-xs font-medium text-gray-500">Avg Days to Close</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $myScore->fast_closes }}</p>
            <p class="text-xs font-medium text-gray-500">Fast Closes</p>
        </div>
    </div>
    @endif
</div>
