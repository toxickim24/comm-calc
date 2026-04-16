<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Dashboard</h2>
            <p class="text-sm text-gray-500">Welcome back, {{ auth()->user()->name }} — {{ now()->format('F Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('export.commission-statement', ['month' => now()->format('Y-m')]) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
               data-tippy-content="Commission Statement PDF">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Commission PDF
            </a>
            <a href="{{ route('export.payout-history', ['month' => now()->format('Y-m')]) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50"
               data-tippy-content="Payout History Excel">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Payouts Excel
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @if($stats['total_users'] !== null)
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-100">
                    <svg class="h-5 w-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Deals This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['month_deals'] }}</p>
                    <p class="text-xs text-green-600">{{ $stats['month_closed'] }} closed</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Commission This Month</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['month_commission'], 0) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-100">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Pipeline Value</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['month_pipeline_value'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Commission Trend (CSS bar chart) --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Commission Trend (6 Months)</h3>
            @php
                $maxCommission = max(array_column($commissionTrend, 'total'));
                $maxCommission = $maxCommission > 0 ? $maxCommission : 1;
            @endphp
            <div class="flex items-end gap-2" style="height: 160px;">
                @foreach($commissionTrend as $month)
                <div class="flex flex-1 flex-col items-center gap-1">
                    <span class="text-xs font-medium text-gray-700">
                        @if($month['total'] > 0) ${{ number_format($month['total'], 0) }} @endif
                    </span>
                    <div class="w-full rounded-t-md transition-all duration-500 {{ $month['total'] > 0 ? 'bg-brand-500' : 'bg-gray-100' }}"
                         style="height: {{ $month['total'] > 0 ? max(($month['total'] / $maxCommission) * 120, 4) : 4 }}px;">
                    </div>
                    <span class="text-xs text-gray-500">{{ $month['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Deal Pipeline --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Deal Pipeline — {{ now()->format('M Y') }}</h3>
            @php $totalDeals = array_sum(array_column($pipeline, 'count')); @endphp
            <div class="space-y-3">
                @foreach($pipeline as $stage)
                @php
                    $pct = $totalDeals > 0 ? ($stage['count'] / $totalDeals) * 100 : 0;
                    $barColors = [
                        'gray' => 'bg-gray-400',
                        'blue' => 'bg-blue-500',
                        'yellow' => 'bg-yellow-500',
                        'green' => 'bg-green-500',
                        'red' => 'bg-red-500',
                    ];
                    $barColor = $barColors[$stage['color']] ?? 'bg-gray-400';
                @endphp
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-700">{{ $stage['label'] }}</span>
                        <span class="font-semibold text-gray-900">{{ $stage['count'] }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100">
                        <div class="h-2 rounded-full {{ $barColor }} transition-all duration-500" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($totalDeals === 0)
                <p class="mt-4 text-center text-sm text-gray-400">No deals this month</p>
            @endif
        </div>

        {{-- Leaderboard (Admin/Manager) --}}
        @if($leaderboard !== null)
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Leaderboard — {{ now()->format('M Y') }}</h3>
            @if(count($leaderboard) > 0)
            <div class="space-y-3">
                @foreach($leaderboard as $index => $rep)
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold
                        {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-gray-200 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $rep['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $rep['deals_closed'] }} {{ Str::plural('deal', $rep['deals_closed']) }} closed</p>
                    </div>
                    <span class="text-sm font-bold {{ $rep['commission'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                        ${{ number_format($rep['commission'], 0) }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
                <p class="text-center text-sm text-gray-400">No data yet</p>
            @endif
        </div>
        @endif

        {{-- Recent Activity --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Recent Activity</h3>
            @if(count($recentActivity) > 0)
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                <div class="flex items-start gap-3">
                    @php
                        $dotColors = [
                            'green' => 'bg-green-500',
                            'red' => 'bg-red-500',
                            'blue' => 'bg-blue-500',
                            'amber' => 'bg-amber-500',
                            'gray' => 'bg-gray-400',
                        ];
                        $dotColor = $dotColors[$activity['color']] ?? 'bg-gray-400';
                    @endphp
                    <div class="mt-1.5 h-2 w-2 flex-shrink-0 rounded-full {{ $dotColor }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">{{ $activity['user'] }}</span>
                            {{ $activity['action'] }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
                <p class="text-center text-sm text-gray-400">No recent activity</p>
            @endif
        </div>
    </div>

    {{-- Year-to-Date Summary --}}
    @if(!empty($ytdSummary))
    <div class="mt-6">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Year-to-Date Earnings — {{ now()->year }}</h3>
                @if(!auth()->user()->isSalesRep() && count($ytdSummary) > 0 && isset($ytdSummary[0]['id']))
                <span class="text-xs text-gray-400">Click PDF to download per-rep statement</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rep</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Commission</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">SPIFF</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                            @if(!auth()->user()->isSalesRep())
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Export</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ytdSummary as $row)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $row['name'] }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-700">${{ number_format($row['commission'], 0) }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-700">${{ number_format($row['spiff'], 0) }}</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-brand-600">${{ number_format($row['total'], 0) }}</td>
                            @if(!auth()->user()->isSalesRep() && isset($row['id']))
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('export.rep-commission-statement', ['repId' => $row['id'], 'month' => now()->format('Y-m')]) }}"
                                   class="text-xs font-medium text-brand-600 hover:text-brand-700"
                                   data-tippy-content="Download {{ $row['name'] }}'s commission PDF">
                                    PDF
                                </a>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    @if(count($ytdSummary) > 1)
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-6 py-3 text-sm text-gray-700">Team Total</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-900">${{ number_format(array_sum(array_column($ytdSummary, 'commission')), 0) }}</td>
                            <td class="px-6 py-3 text-right text-sm text-gray-900">${{ number_format(array_sum(array_column($ytdSummary, 'spiff')), 0) }}</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-brand-600">${{ number_format(array_sum(array_column($ytdSummary, 'total')), 0) }}</td>
                            @if(!auth()->user()->isSalesRep())
                            <td></td>
                            @endif
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
