<?php
namespace App\Http\Controllers;
use App\Models\Report;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class ReportApprovalController extends Controller {
    public function pending(): JsonResponse {
        Gate::authorize('management');
        return response()->json(['data'=>Report::with('staff')->where('status','SUBMITTED')->latest()->paginate(20)]);
    }
    public function approve(Request $request, Report $report): JsonResponse {
        Gate::authorize('management');
        abort_if($report->status!=='SUBMITTED',422,'只能核准已送審的報表。');
        $report->update(['status'=>'APPROVED']);
        return response()->json(['data'=>$report->fresh()]);
    }
    public function reject(Request $request, Report $report): JsonResponse {
        Gate::authorize('management');
        abort_if($report->status!=='SUBMITTED',422,'只能退回已送審的報表。');
        $report->update(['status'=>'REJECTED']);
        return response()->json(['data'=>$report->fresh()]);
    }
}
