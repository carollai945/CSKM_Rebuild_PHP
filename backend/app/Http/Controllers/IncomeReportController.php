<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
/**
 * E01 收入報表
 *
 * 功能編號：E01
 * 對應文件：docs/sdd/e01-income-report-sdd.md
 */
class IncomeReportController extends Controller {
    public function index(Request $request): JsonResponse {
        Gate::authorize('is-finance');
        $query = Payment::selectRaw('DATE(payment_date) as date, SUM(amount) as total, COUNT(*) as count, status')
            ->when($request->start_date,fn($q,$v)=>$q->whereDate('payment_date','>=',$v))
            ->when($request->end_date,fn($q,$v)=>$q->whereDate('payment_date','<=',$v))
            ->where('status','ACADEMIC_CONFIRMED')
            ->groupBy('date','status')
            ->orderBy('date','desc');
        return response()->json(['data'=>$query->get()]);
    }
}
