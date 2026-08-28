<?php
namespace App\Http\Controllers;
use App\Models\ApprovalActionLog;
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
    public function pending(Request $request): JsonResponse {
        Gate::authorize('management');
        $query = Report::with(['staff.user'])
            ->where('status', 'SUBMITTED')
            ->when($request->filled('report_type'), fn ($q) => $q->where('report_type', $request->report_type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('report_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('report_date', '<=', $request->to))
            ->when($request->filled('keyword'), fn ($q) => $q->whereHas('staff', fn ($sq) => $sq->where('name', 'like', '%' . $request->keyword . '%')))
            ->latest('report_date')
            ->latest('id');
        return response()->json(['data' => $query->paginate(20)]);
    }
    public function approve(Request $request, Report $report): JsonResponse {
        Gate::authorize('management');
        abort_if($report->status!=='SUBMITTED',422,'只能核准已送審的報表。');
        $report->update(['status'=>'APPROVED']);
        ApprovalActionLog::create(['related_type'=>'report','related_id'=>$report->id,'actor_id'=>$request->user()->id,'action'=>'APPROVE']);
        return response()->json(['data'=>$report->fresh()]);
    }
    public function reject(Request $request, Report $report): JsonResponse {
        Gate::authorize('management');
        abort_if($report->status!=='SUBMITTED',422,'只能退回已送審的報表。');
        $rejectReason = $request->validate(['reject_reason'=>'nullable|string'])['reject_reason']??null;
        $report->update(['status'=>'REJECTED']);
        ApprovalActionLog::create(['related_type'=>'report','related_id'=>$report->id,'actor_id'=>$request->user()->id,'action'=>'REJECT','comment'=>$rejectReason]);
        return response()->json(['data'=>$report->fresh()]);
    }

    public function batchApprove(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $ids = Report::whereIn('id', $request->ids)
            ->where('status', 'SUBMITTED')
            ->pluck('id')
            ->all();
        $count = Report::whereIn('id', $ids)->update(['status' => 'APPROVED']);
        foreach ($ids as $id) {
            ApprovalActionLog::create([
                'related_type' => 'report',
                'related_id' => $id,
                'actor_id' => $request->user()->id,
                'action' => 'APPROVE',
            ]);
        }
        return response()->json(['data' => ['approved_count' => $count]]);
    }

    public function batchReject(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer', 'reject_reason' => 'nullable|string']);
        $ids = Report::whereIn('id', $request->ids)
            ->where('status', 'SUBMITTED')
            ->pluck('id')
            ->all();
        $count = Report::whereIn('id', $ids)->update(['status' => 'REJECTED']);
        foreach ($ids as $id) {
            ApprovalActionLog::create([
                'related_type' => 'report',
                'related_id' => $id,
                'actor_id' => $request->user()->id,
                'action' => 'REJECT',
                'comment' => $request->reject_reason,
            ]);
        }
        return response()->json(['data' => ['rejected_count' => $count]]);
    }
}
