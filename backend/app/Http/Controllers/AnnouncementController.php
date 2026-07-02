<?php
namespace App\Http\Controllers;
use App\Models\Announcement;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class AnnouncementController extends Controller {
    private function myStaffId(Request $r): ?int { return Staff::where('user_id',$r->user()->id)->value('id'); }
    public function index(Request $request): JsonResponse {
        $q = Announcement::when($request->status,fn($q,$v)=>$q->where('status',$v))->latest();
        return response()->json(['data'=>$q->paginate(20)]);
    }
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['title'=>'required|string|max:200','content'=>'nullable|string','target_scope'=>'nullable|string|max:30','publish_at'=>'nullable|date']);
        $v['staff_id']=$this->myStaffId($request);
        return response()->json(['data'=>Announcement::create($v)],201);
    }
    public function show(Announcement $announcement): JsonResponse { return response()->json(['data'=>$announcement->load('staff')]); }
    public function update(Request $request, Announcement $announcement): JsonResponse {
        abort_if($announcement->status==='PUBLISHED',422,'已發布的公告不可修改。');
        $announcement->update($request->validate(['title'=>'sometimes|required|string|max:200','content'=>'nullable|string','publish_at'=>'nullable|date']));
        return response()->json(['data'=>$announcement->fresh()]);
    }
    public function destroy(Announcement $announcement): JsonResponse {
        abort_if($announcement->status==='PUBLISHED',422,'已發布的公告不可刪除。');
        $announcement->delete();
        return response()->json(null,204);
    }
}
