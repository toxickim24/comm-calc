<?php

namespace App\Http\Controllers;

use App\Enums\DealStatus;
use App\Models\CommissionPayout;
use App\Models\Deal;
use App\Models\SpiffPayout;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function commissionStatementPdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');
        $user = auth()->user();

        $query = CommissionPayout::with(['user', 'deal'])
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month);

        if ($user->isSalesRep()) {
            $query->where('user_id', $user->id);
        }

        $payouts = $query->orderBy('user_id')->get();
        $grouped = $payouts->groupBy('user_id');
        $grandTotal = $payouts->sum('total_payout');

        $pdf = Pdf::loadView('exports.commission-statement', [
            'payouts' => $payouts,
            'grouped' => $grouped,
            'monthDate' => $monthDate,
            'grandTotal' => $grandTotal,
        ]);

        return $pdf->download("commission-statement-{$month}.pdf");
    }

    public function repCommissionStatementPdf(Request $request, int $repId)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');
        $user = auth()->user();

        // Sales reps can only export their own
        if ($user->isSalesRep() && $user->id !== $repId) {
            abort(403);
        }

        $rep = User::findOrFail($repId);

        $payouts = CommissionPayout::with(['user', 'deal'])
            ->where('user_id', $repId)
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->get();

        $spiffPayout = SpiffPayout::where('user_id', $repId)
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->first();

        $commissionTotal = $payouts->sum('total_payout');
        $spiffTotal = $spiffPayout?->total_spiff ?? 0;

        $pdf = Pdf::loadView('exports.rep-commission-statement', [
            'rep' => $rep,
            'payouts' => $payouts,
            'monthDate' => $monthDate,
            'commissionTotal' => $commissionTotal,
            'spiffPayout' => $spiffPayout,
            'spiffTotal' => $spiffTotal,
            'grandTotal' => $commissionTotal + $spiffTotal,
        ]);

        $repSlug = str_replace(' ', '-', strtolower($rep->name));
        return $pdf->download("commission-{$repSlug}-{$month}.pdf");
    }

    public function spiffReportPdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');

        $payouts = SpiffPayout::with('user')
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month)
            ->orderByDesc('total_spiff')
            ->get();

        $grandTotal = $payouts->sum('total_spiff');

        $pdf = Pdf::loadView('exports.spiff-report', [
            'payouts' => $payouts,
            'monthDate' => $monthDate,
            'grandTotal' => $grandTotal,
        ]);

        return $pdf->download("spiff-report-{$month}.pdf");
    }

    public function dealLogExcel(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');
        $user = auth()->user();

        $query = Deal::with(['user', 'commissionPayout'])
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month);

        if ($user->isSalesRep()) {
            $query->where('user_id', $user->id);
        }

        $deals = $query->orderBy('created_at')->get();

        return Excel::download(
            new DealLogExport($deals),
            "deal-log-{$month}.xlsx"
        );
    }

    public function payoutHistoryExcel(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');
        $user = auth()->user();

        $query = CommissionPayout::with(['user', 'deal'])
            ->whereYear('month', $monthDate->year)
            ->whereMonth('month', $monthDate->month);

        if ($user->isSalesRep()) {
            $query->where('user_id', $user->id);
        }

        $payouts = $query->orderBy('user_id')->get();

        return Excel::download(
            new PayoutHistoryExport($payouts),
            "payout-history-{$month}.xlsx"
        );
    }
}

class DealLogExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected $deals) {}

    public function collection() { return $this->deals; }

    public function headings(): array
    {
        return ['Rep', 'Client', 'Month', 'Status', 'Contract Value', 'GM%', 'Days to Close', 'Fast Close', 'Commission', 'Notes'];
    }

    public function map($deal): array
    {
        return [
            $deal->user->name,
            $deal->client_name,
            $deal->month->format('M Y'),
            $deal->deal_status->label(),
            number_format($deal->sold_contract_value, 2),
            number_format($deal->estimated_gm_percent, 1) . '%',
            $deal->days_to_close ?? '--',
            $deal->is_fast_close ? 'Yes' : 'No',
            $deal->commissionPayout ? '$' . number_format($deal->commissionPayout->total_payout, 2) : '--',
            $deal->notes ?? '',
        ];
    }
}

class PayoutHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected $payouts) {}

    public function collection() { return $this->payouts; }

    public function headings(): array
    {
        return ['Rep', 'Client', 'Month', 'Contract Value', 'GM%', 'Tier', 'Base Commission', 'Surplus Bonus', 'Fast Close Bonus', 'Total Payout'];
    }

    public function map($payout): array
    {
        return [
            $payout->user->name,
            $payout->deal?->client_name ?? '--',
            $payout->month->format('M Y'),
            '$' . number_format($payout->sold_contract_value, 2),
            number_format($payout->gm_percent, 1) . '%',
            $payout->tier,
            '$' . number_format($payout->base_commission, 2),
            '$' . number_format($payout->surplus_bonus, 2),
            '$' . number_format($payout->fast_close_bonus, 2),
            '$' . number_format($payout->total_payout, 2),
        ];
    }
}
