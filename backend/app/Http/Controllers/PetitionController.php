<?php
namespace App\Http\Controllers;
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

    /**
     * 取得目前登入教職員的簽呈申請列表。
     */
    public function index(Request $request): JsonResponse {
        $query = Petition::where('staff_id',$this->myStaffId($request))
            ->when($request->status,fn($q,$v)=>$q->where('status',$v))->latest();
        return response()->json(['data'=>$query->paginate(20)]);
    }
    /**
     * 建立新的簽呈申請單。
     */
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['title'=>'required|string|max:200','content'=>'nullable|string']);
        $v['staff_id'] = $this->myStaffId($request);
        return response()->json(['data'=>Petition::create($v)],201);
    }
    /**
     * 檢視單筆簽呈申請與簽核資訊。
     */
    public function show(Petition $petition): JsonResponse { return response()->json(['data'=>$petition->load(['staff','approver'])]); }
    /**
     * 修改尚未簽核完成的簽呈申請單。
     */
    public function update(Request $request, Petition $petition): JsonResponse {
        abort_if($petition->status!=='PENDING',422,'只能修改待審中的簽呈。');
        $petition->update($request->validate(['title'=>'sometimes|required|string|max:200','content'=>'nullable|string']));
        return response()->json(['data'=>$petition->fresh()]);
    }
    /**
     * 取消尚未簽核完成的簽呈申請單。
     */
    public function destroy(Petition $petition): JsonResponse {
        abort_if($petition->status!=='PENDING',422,'只能取消待審中的簽呈。');
        $petition->update(['status'=>'CANCELLED']);
        return response()->json(null,204);
    }
}
