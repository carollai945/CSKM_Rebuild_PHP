<?php
namespace App\Http\Controllers;
use App\Models\Petition;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class PetitionApprovalController extends Controller {
    public function pending(Request $request): JsonResponse {
        Gate::authorize('management');
        return response()->json(['data'=>Petition::with('staff')->where('status','PENDING')->latest()->paginate(20)]);
    }
    public function approve(Request $request, Petition $petition): JsonResponse {
        Gate::authorize('management');
        abort_if($petition->status!=='PENDING',422,'只能核准待審中的簽呈。');
        $petition->update(['status'=>'APPROVED','approved_by'=>Staff::where('user_id',$request->user()->id)->value('id')]);
        return response()->json(['data'=>$petition->fresh()]);
    }
    public function reject(Request $request, Petition $petition): JsonResponse {
        Gate::authorize('management');
        abort_if($petition->status!=='PENDING',422,'只能退回待審中的簽呈。');
        $petition->update(['status'=>'REJECTED','reject_reason'=>$request->validate(['reject_reason'=>'nullable|string'])['reject_reason']??null]);
        return response()->json(['data'=>$petition->fresh()]);
    }
}
