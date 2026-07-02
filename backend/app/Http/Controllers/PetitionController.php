<?php
namespace App\Http\Controllers;
use App\Models\Petition;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return response()->json(['data'=>Petition::create($v)],201);
    }
    public function show(Petition $petition): JsonResponse { return response()->json(['data'=>$petition->load(['staff','approver'])]); }
    public function update(Request $request, Petition $petition): JsonResponse {
        abort_if($petition->status!=='PENDING',422,'只能修改待審中的簽呈。');
        $petition->update($request->validate(['title'=>'sometimes|required|string|max:200','content'=>'nullable|string']));
        return response()->json(['data'=>$petition->fresh()]);
    }
    public function destroy(Petition $petition): JsonResponse {
        abort_if($petition->status!=='PENDING',422,'只能取消待審中的簽呈。');
        $petition->update(['status'=>'CANCELLED']);
        return response()->json(null,204);
    }
}
