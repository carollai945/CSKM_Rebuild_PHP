<?php
namespace App\Http\Controllers;
use App\Models\ApprovalActionLog;
use App\Models\Petition;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A04 簽呈申請
 *
 * 功能編號：A04
 * 對應文件：docs/sdd/a04-petition-sdd.md
 */
class PetitionController extends Controller {
    private function myStaffId(Request $r): ?int { return Staff::where('user_id',$r->user()->id)->value('id'); }

    public function index(Request $request): JsonResponse {
        $query = Petition::where('staff_id',$this->myStaffId($request))
            ->when($request->status,fn($q,$v)=>$q->where('status',$v))->latest();
        return response()->json(['data'=>$query->paginate(20)]);
    }
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['title'=>'required|string|max:200','content'=>'nullable|string']);
        $v['staff_id'] = $this->myStaffId($request);
        $petition = Petition::create($v);
        ApprovalActionLog::create(['related_type'=>'petition','related_id'=>$petition->id,'actor_id'=>$request->user()->id,'action'=>'SUBMIT']);
        return response()->json(['data'=>$petition],201);
    }
    public function show(Petition $petition): JsonResponse { return response()->json(['data'=>$petition->load(['staff','approver'])]); }
    public function update(Request $request, Petition $petition): JsonResponse {
        abort_if($petition->status!=='DRAFT',422,'只能修改草稿狀態的簽呈。');
        $petition->update($request->validate(['title'=>'sometimes|required|string|max:200','content'=>'nullable|string']));
        return response()->json(['data'=>$petition->fresh()]);
    }
    public function destroy(Petition $petition): JsonResponse {
        abort_if($petition->status!=='DRAFT',422,'只能刪除草稿狀態的簽呈。');
        $petition->delete();
        return response()->json(null,204);
    }
    public function submit(Request $request, Petition $petition): JsonResponse {
        abort_if($petition->status!=='DRAFT',422,'只有草稿狀態的簽呈可以送審。');
        $petition->update(['status'=>'PENDING']);
        return response()->json(['data'=>$petition->fresh()]);
    }
    public function cancel(Request $request, Petition $petition): JsonResponse {
        abort_if($petition->staff_id!==$this->myStaffId($request),403,'無權限取消此簽呈。');
        abort_if($petition->status!=='PENDING',422,'只有待審狀態的簽呈可以取消。');
        $petition->update(['status'=>'CANCELLED']);
        return response()->json(['data'=>$petition->fresh()]);
    }
}
