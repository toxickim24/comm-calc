<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SPIFF Report — {{ $monthDate->format('F Y') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #4a7342; padding-bottom: 16px; margin-bottom: 24px; }
        .header table { margin-bottom: 0; border: none; }
        .header td { border: none; padding: 0; vertical-align: middle; }
        .header .logo { width: 200px; }
        .header .logo img { width: 180px; height: auto; }
        .header .info { padding-left: 16px; }
        .header .doc-title { font-size: 15px; font-weight: bold; color: #4a7342; margin: 0; }
        .header .meta { text-align: right; font-size: 11px; color: #6b7280; }
        .header .meta .period { font-size: 16px; font-weight: bold; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f3f4f6; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-green { color: #16a34a; }
        .override { background: #fffbeb; }
        .grand-total { background: #4a7342; color: white; font-size: 14px; font-weight: bold; }
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 10px; }
        .note { font-size: 10px; color: #92400e; font-style: italic; }
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
                    <p class="doc-title">Monthly SPIFF Report</p>
                </td>
                <td class="meta">
                    <p class="period">{{ $monthDate->format('F Y') }}</p>
                    <p>Generated {{ now()->format('M d, Y') }}</p>
                    <p>{{ $payouts->count() }} {{ Str::plural('rep', $payouts->count()) }} | ${{ number_format($grandTotal, 2) }} total</p>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <tr>
            <th>Rep</th>
            <th class="text-right">Appts</th>
            <th class="text-right">Closed</th>
            <th class="text-right">Close %</th>
            <th class="text-right">Improve</th>
            <th class="text-right">Target</th>
            <th class="text-right">Fast Close</th>
            <th class="text-right">Top Rate</th>
            <th class="text-right">Total</th>
        </tr>
        @foreach($payouts as $payout)
        <tr @if($payout->is_override) class="override" @endif>
            <td>
                {{ $payout->user->name }}
                @if($payout->is_override) <span class="note">(Override)</span> @endif
            </td>
            <td class="text-right">{{ $payout->appointments }}</td>
            <td class="text-right">{{ $payout->deals_closed }}</td>
            <td class="text-right">{{ number_format($payout->close_rate, 1) }}%</td>
            <td class="text-right">${{ number_format($payout->improvement_bonus, 0) }}</td>
            <td class="text-right">${{ number_format($payout->target_bonus, 0) }}</td>
            <td class="text-right">${{ number_format($payout->fast_close_bonus, 0) }}</td>
            <td class="text-right">${{ number_format($payout->highest_close_rate_bonus, 0) }}</td>
            <td class="text-right font-bold text-green">${{ number_format($payout->total_spiff, 2) }}</td>
        </tr>
        @if($payout->is_override && $payout->override_notes)
        <tr>
            <td colspan="9" class="note">Note: {{ $payout->override_notes }}</td>
        </tr>
        @endif
        @endforeach
        <tr class="grand-total">
            <td colspan="8" class="text-right">Grand Total</td>
            <td class="text-right">${{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <strong>Bayside Pavers</strong> — Commission Manager<br>
        This is a system-generated document. Generated on {{ now()->format('M d, Y \a\t g:i A') }}.
    </div>
</body>
</html>
