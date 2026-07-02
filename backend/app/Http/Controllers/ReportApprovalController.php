<?php
namespace App\Http\Controllers;
use App\Models\Report;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
/**
 * D03 報表審核
 *
 * 功能編號：D03
 * 對應文件：docs/sdd/d03-report-approval-sdd.md
 */
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

    public function batchApprove(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $staffId = \App\Models\Staff::where('user_id', $request->user()->id)->value('id');
        $count = Report::whereIn('id', $request->ids)->where('status', 'PENDING')
            ->update(['status' => 'APPROVED', 'approved_by' => $staffId]);
        return response()->json(['data' => ['approved_count' => $count]]);
    }

    public function batchReject(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer', 'reject_reason' => 'nullable|string']);
        $count = Report::whereIn('id', $request->ids)->where('status', 'PENDING')
            ->update(['status' => 'REJECTED', 'reject_reason' => $request->reject_reason]);
        return response()->json(['data' => ['rejected_count' => $count]]);
    }
}
