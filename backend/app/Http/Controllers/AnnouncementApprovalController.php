<?php
namespace App\Http\Controllers;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
/**
 * D02 公告審核
 *
 * 功能編號：D02
 * 對應文件：docs/sdd/d02-announcement-approval-sdd.md
 */
class AnnouncementApprovalController extends Controller {
    public function pending(): JsonResponse {
        Gate::authorize('management');
        return response()->json(['data'=>Announcement::with('staff')->where('status','PENDING_APPROVAL')->latest()->paginate(20)]);
    }
    public function approve(Announcement $announcement): JsonResponse {
        Gate::authorize('management');
        abort_if($announcement->status!=='PENDING_APPROVAL',422,'只能核准待審公告。');
        $announcement->update(['status'=>'PUBLISHED']);
        return response()->json(['data'=>$announcement->fresh()]);
    }
    public function reject(Request $request, Announcement $announcement): JsonResponse {
        Gate::authorize('management');
        abort_if($announcement->status!=='PENDING_APPROVAL',422,'只能退回待審公告。');
        $announcement->update(['status'=>'DRAFT']);
        return response()->json(['data'=>$announcement->fresh()]);
    }

    public function batchApprove(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        $staffId = \App\Models\Staff::where('user_id', $request->user()->id)->value('id');
        $count = Announcement::whereIn('id', $request->ids)->where('status', 'PENDING')
            ->update(['status' => 'APPROVED', 'approved_by' => $staffId]);
        return response()->json(['data' => ['approved_count' => $count]]);
    }

    public function batchReject(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer', 'reject_reason' => 'nullable|string']);
        $count = Announcement::whereIn('id', $request->ids)->where('status', 'PENDING')
            ->update(['status' => 'REJECTED', 'reject_reason' => $request->reject_reason]);
        return response()->json(['data' => ['rejected_count' => $count]]);
    }
}
