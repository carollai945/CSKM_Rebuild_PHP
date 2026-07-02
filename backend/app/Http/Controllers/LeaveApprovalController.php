<?php
namespace App\Http\Controllers;
use App\Models\LeaveRequest;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
}
