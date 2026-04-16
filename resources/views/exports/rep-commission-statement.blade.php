<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Commission Statement — {{ $rep->name }} — {{ $monthDate->format('F Y') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #4a7342; padding-bottom: 16px; margin-bottom: 24px; }
        .header table { margin-bottom: 0; border: none; }
        .header td { border: none; padding: 0; vertical-align: middle; }
        .header .logo { width: 100px; }
        .header .logo img { width: 90px; height: auto; }
        .header .info { padding-left: 16px; }
        .header .doc-title { font-size: 15px; font-weight: bold; color: #4a7342; margin: 0; }
        .header .doc-sub { font-size: 12px; color: #6b7280; margin: 2px 0 0 0; }
        .header .meta { text-align: right; font-size: 11px; color: #6b7280; }
        .header .meta .period { font-size: 16px; font-weight: bold; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; text-align: left; padding: 8px 10px; font-size: 11px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-green { color: #16a34a; }
        .summary-box { background: #f0f7ee; border: 1px solid #4a7342; border-radius: 6px; padding: 16px; margin-bottom: 20px; }
        .summary-box h3 { margin: 0 0 12px 0; font-size: 13px; color: #4a7342; }
        .summary-row { display: flex; justify-content: space-between; padding: 4px 0; }
        .summary-table { width: 100%; }
        .summary-table td { border: none; padding: 4px 10px; }
        .summary-label { color: #6b7280; }
        .summary-value { font-weight: bold; color: #1f2937; text-align: right; }
        .grand-total-row td { background: #4a7342; color: white; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 10px; }
        .spiff-section { margin-top: 24px; }
        .spiff-section h3 { font-size: 13px; color: #4a7342; margin-bottom: 8px; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    @if($logoData)
                        <img src="{{ $logoData }}" alt="Logo">
                    @endif
                </td>
                <td class="info">
                    <p class="doc-title">Commission Statement</p>
                    <p class="doc-sub">{{ $rep->name }}</p>
                </td>
                <td class="meta">
                    <p class="period">{{ $monthDate->format('F Y') }}</p>
                    <p>Generated {{ now()->format('M d, Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Summary Box --}}
    <div class="summary-box">
        <h3>Monthly Summary</h3>
        <table class="summary-table">
            <tr>
                <td class="summary-label">Commission ({{ $payouts->count() }} {{ Str::plural('deal', $payouts->count()) }})</td>
                <td class="summary-value">${{ number_format($commissionTotal, 2) }}</td>
            </tr>
            @if($spiffPayout)
            <tr>
                <td class="summary-label">SPIFF Bonus</td>
                <td class="summary-value">${{ number_format($spiffTotal, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="summary-label" style="font-weight:bold; color:#1f2937; border-top: 1px solid #d1d5db; padding-top: 8px;">Total Earnings</td>
                <td class="summary-value" style="color:#4a7342; font-size: 16px; border-top: 1px solid #d1d5db; padding-top: 8px;">${{ number_format($grandTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Commission Details --}}
    @if($payouts->isNotEmpty())
    <table>
        <tr>
            <th>Client</th>
            <th class="text-right">Contract Value</th>
            <th class="text-right">GM%</th>
            <th class="text-right">Base</th>
            <th class="text-right">Surplus</th>
            <th class="text-right">Fast Close</th>
            <th class="text-right">Total</th>
        </tr>
        @foreach($payouts as $payout)
        <tr>
            <td>{{ $payout->deal?->client_name ?? 'N/A' }}</td>
            <td class="text-right">${{ number_format($payout->sold_contract_value, 2) }}</td>
            <td class="text-right">{{ number_format($payout->gm_percent, 1) }}%</td>
            <td class="text-right">${{ number_format($payout->base_commission, 2) }}</td>
            <td class="text-right">${{ number_format($payout->surplus_bonus, 2) }}</td>
            <td class="text-right">${{ number_format($payout->fast_close_bonus, 2) }}</td>
            <td class="text-right font-bold text-green">${{ number_format($payout->total_payout, 2) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6" class="text-right font-bold">Commission Subtotal</td>
            <td class="text-right font-bold">${{ number_format($commissionTotal, 2) }}</td>
        </tr>
    </table>
    @else
    <p style="color: #6b7280; text-align: center; padding: 20px;">No commission payouts for this month.</p>
    @endif

    {{-- SPIFF Details --}}
    @if($spiffPayout)
    <div class="spiff-section">
        <h3>SPIFF Bonus Breakdown</h3>
        <table>
            <tr>
                <th>Component</th>
                <th class="text-right">Details</th>
                <th class="text-right">Amount</th>
            </tr>
            <tr>
                <td>Improvement Bonus</td>
                <td class="text-right">{{ number_format($spiffPayout->improvement_points, 1) }} pp improvement</td>
                <td class="text-right">${{ number_format($spiffPayout->improvement_bonus, 2) }}</td>
            </tr>
            <tr>
                <td>Target Close Rate Bonus</td>
                <td class="text-right">{{ number_format($spiffPayout->close_rate, 1) }}% close rate</td>
                <td class="text-right">${{ number_format($spiffPayout->target_bonus, 2) }}</td>
            </tr>
            <tr>
                <td>Fast Close Bonus</td>
                <td class="text-right">{{ $spiffPayout->fast_close_count }} {{ Str::plural('deal', $spiffPayout->fast_close_count) }}</td>
                <td class="text-right">${{ number_format($spiffPayout->fast_close_bonus, 2) }}</td>
            </tr>
            <tr>
                <td>Highest Close Rate</td>
                <td class="text-right">—</td>
                <td class="text-right">${{ number_format($spiffPayout->highest_close_rate_bonus, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right font-bold">SPIFF Subtotal</td>
                <td class="text-right font-bold">${{ number_format($spiffTotal, 2) }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        <strong>Bayside Pavers</strong> — Commission Manager<br>
        Confidential — prepared for {{ $rep->name }}. Generated on {{ now()->format('M d, Y \a\t g:i A') }}.
    </div>
</body>
</html>
