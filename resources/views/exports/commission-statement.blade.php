<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Commission Statement — {{ $monthDate->format('F Y') }}</title>
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
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #f3f4f6; text-align: left; padding: 8px 10px; font-size: 11px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-green { color: #16a34a; }
        .rep-header { background: #f0f7ee; padding: 8px 10px; font-weight: bold; font-size: 13px; color: #4a7342; border-bottom: 1px solid #d1d5db; }
        .total-row { background: #f9fafb; font-weight: bold; }
        .grand-total { background: #4a7342; color: white; font-size: 14px; }
        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 10px; }
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
                </td>
                <td class="meta">
                    <p class="period">{{ $monthDate->format('F Y') }}</p>
                    <p>Generated {{ now()->format('M d, Y') }}</p>
                    <p>{{ $payouts->count() }} {{ Str::plural('payout', $payouts->count()) }} | ${{ number_format($grandTotal, 2) }} total</p>
                </td>
            </tr>
        </table>
    </div>

    @foreach($grouped as $userId => $repPayouts)
    @php $rep = $repPayouts->first()->user; @endphp
    <table>
        <tr><td colspan="6" class="rep-header">{{ $rep->name }}</td></tr>
        <tr>
            <th>Client</th>
            <th class="text-right">Contract Value</th>
            <th class="text-right">GM%</th>
            <th class="text-right">Base</th>
            <th class="text-right">Bonuses</th>
            <th class="text-right">Total</th>
        </tr>
        @foreach($repPayouts as $payout)
        <tr>
            <td>{{ $payout->deal?->client_name ?? 'N/A' }}</td>
            <td class="text-right">${{ number_format($payout->sold_contract_value, 2) }}</td>
            <td class="text-right">{{ number_format($payout->gm_percent, 1) }}%</td>
            <td class="text-right">${{ number_format($payout->base_commission, 2) }}</td>
            <td class="text-right">${{ number_format($payout->surplus_bonus + $payout->fast_close_bonus, 2) }}</td>
            <td class="text-right font-bold text-green">${{ number_format($payout->total_payout, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="5" class="text-right">Subtotal for {{ $rep->name }}</td>
            <td class="text-right">${{ number_format($repPayouts->sum('total_payout'), 2) }}</td>
        </tr>
    </table>
    @endforeach

    <table>
        <tr class="grand-total">
            <td colspan="5" class="text-right">Grand Total</td>
            <td class="text-right">${{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <strong>Bayside Pavers</strong> — Commission Manager<br>
        This is a system-generated document. Generated on {{ now()->format('M d, Y \a\t g:i A') }}.
    </div>
</body>
</html>
