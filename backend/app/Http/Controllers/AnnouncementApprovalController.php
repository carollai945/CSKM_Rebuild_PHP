<?php
namespace App\Http\Controllers;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
}
