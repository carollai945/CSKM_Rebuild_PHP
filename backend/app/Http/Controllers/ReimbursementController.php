<?php
namespace App\Http\Controllers;
use App\Models\Reimbursement;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class ReimbursementController extends Controller {
    private function myStaffId(Request $r): ?int { return Staff::where('user_id',$r->user()->id)->value('id'); }
    public function index(Request $request): JsonResponse {
        $isFinance = $request->user()->role->value === 'finance' || $request->user()->role->value === 'admin';
        $q = Reimbursement::with('staff')
            ->when(!$isFinance, fn($q)=>$q->where('staff_id',$this->myStaffId($request)))
            ->when($request->status,fn($q,$v)=>$q->where('status',$v))->latest();
        return response()->json(['data'=>$q->paginate(20)]);
    }
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['title'=>'required|string|max:200','amount'=>'required|numeric|min:0','description'=>'nullable|string']);
        $v['staff_id']=$this->myStaffId($request);
        return response()->json(['data'=>Reimbursement::create($v)],201);
    }
    public function show(Reimbursement $reimbursement): JsonResponse { return response()->json(['data'=>$reimbursement->load('staff')]); }
    public function financeConfirm(Request $request, Reimbursement $reimbursement): JsonResponse {
        Gate::authorize('is-finance');
        abort_if($reimbursement->status!=='PENDING',422,'只能確認待審狀態的請款。');
        $reimbursement->update(['status'=>'FINANCE_CONFIRMED','finance_confirmed_by'=>$this->myStaffId($request)]);
        return response()->json(['data'=>$reimbursement->fresh()]);
    }
    public function reject(Request $request, Reimbursement $reimbursement): JsonResponse {
        Gate::authorize('management');
        $v = $request->validate(['reject_reason'=>'nullable|string']);
        $reimbursement->update(['status'=>'REJECTED','reject_reason'=>$v['reject_reason']??null]);
        return response()->json(['data'=>$reimbursement->fresh()]);
    }
}
