<?php
namespace App\Http\Controllers;
use App\Models\LeaveRequest;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * D00 請假單審核
 *
 * 功能編號：D00
 * 對應文件：docs/sdd/d00-leave-approval-sdd.md
 */
class LeaveApprovalController extends Controller {
    public function pending(Request $request): JsonResponse {
        Gate::authorize('management');
        $q = LeaveRequest::with(['staff'])->where('status','PENDING')
            ->when($request->region_id,fn($q,$v)=>$q->whereHas('staff',fn($q)=>$q->where('region_id',$v)))->latest();
        return response()->json(['data'=>$q->paginate(20)]);
    }
    public function approve(Request $request, LeaveRequest $leaveRequest): JsonResponse {
        Gate::authorize('management');
        abort_if($leaveRequest->status!=='PENDING',422,'只能核准待審中的請假單。');
        $staffId = Staff::where('user_id',$request->user()->id)->value('id');
        $leaveRequest->update(['status'=>'APPROVED','approved_by'=>$staffId]);
        return response()->json(['data'=>$leaveRequest->fresh()]);
    }
    public function reject(Request $request, LeaveRequest $leaveRequest): JsonResponse {
        Gate::authorize('management');
        abort_if($leaveRequest->status!=='PENDING',422,'只能退回待審中的請假單。');
        $validated = $request->validate(['reject_reason'=>'nullable|string']);
        $leaveRequest->update(['status'=>'REJECTED','reject_reason'=>$validated['reject_reason']??null]);
        return response()->json(['data'=>$leaveRequest->fresh()]);
    }

    public function batchApprove(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $staffId = \App\Models\Staff::where('user_id', $request->user()->id)->value('id');
        $count = LeaveRequest::whereIn('id', $request->ids)->where('status', 'PENDING')
            ->update(['status' => 'APPROVED', 'approved_by' => $staffId]);
        return response()->json(['data' => ['approved_count' => $count]]);
    }

    public function batchReject(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer', 'reject_reason' => 'nullable|string']);
        $count = LeaveRequest::whereIn('id', $request->ids)->where('status', 'PENDING')
            ->update(['status' => 'REJECTED', 'reject_reason' => $request->reject_reason]);
        return response()->json(['data' => ['rejected_count' => $count]]);
    }
}
