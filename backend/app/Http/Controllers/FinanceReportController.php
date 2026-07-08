<?php
namespace App\Http\Controllers;

use App\Models\InvoiceRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * E01 財務報表 — 請款彙整 & 繳費明細彙整
 *
 * 功能編號：E01-1, E02-1
 * 對應文件：docs/api-design/CSKM_API_Design.md 第 9 節
 */
class FinanceReportController extends Controller
{
    /**
     * GET /api/v1/finance-reports/invoices
     * 請款彙整報表，支援 from_date / to_date / department_id / region_id 篩選
     */
    public function invoices(Request $request): JsonResponse
    {
        Gate::authorize('is-finance-report');

        $query = InvoiceRequest::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->when($request->from_date, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to_date,   fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->department_id, fn ($q, $v) => $q->whereHas('staff', fn ($s) => $s->where('department_id', $v)))
            ->when($request->region_id,     fn ($q, $v) => $q->whereHas('staff', fn ($s) => $s->where('region_id', $v)))
            ->groupBy('status')
            ->orderBy('status');

        return response()->json(['data' => $query->get()]);
    }

    /**
     * GET /api/v1/finance-reports/payments
     * 繳費明細彙整報表，支援 from_date / to_date / student_id / fee_item_id 篩選
     */
    public function payments(Request $request): JsonResponse
    {
        Gate::authorize('is-finance-report');

        $query = Payment::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->when($request->from_date,  fn ($q, $v) => $q->whereDate('payment_date', '>=', $v))
            ->when($request->to_date,    fn ($q, $v) => $q->whereDate('payment_date', '<=', $v))
            ->when($request->student_id, fn ($q, $v) => $q->where('student_id', $v))
            ->when($request->fee_item_id, fn ($q, $v) => $q->where('fee_item_id', $v))
            ->groupBy('status')
            ->orderBy('status');

        return response()->json(['data' => $query->get()]);
    }
}
