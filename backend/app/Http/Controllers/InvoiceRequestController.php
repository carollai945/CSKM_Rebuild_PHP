<?php
namespace App\Http\Controllers;
use App\Models\InvoiceRequest;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class InvoiceRequestController extends Controller {
    private function myStaffId(Request $r): ?int { return Staff::where('user_id',$r->user()->id)->value('id'); }
    public function index(Request $request): JsonResponse {
        $q = InvoiceRequest::where('staff_id',$this->myStaffId($request))->when($request->status,fn($q,$v)=>$q->where('status',$v))->latest();
        return response()->json(['data'=>$q->paginate(20)]);
    }
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['title'=>'required|string|max:200','amount'=>'required|numeric|min:0','description'=>'nullable|string']);
        $v['staff_id']=$this->myStaffId($request);
        return response()->json(['data'=>InvoiceRequest::create($v)],201);
    }
    public function show(InvoiceRequest $invoiceRequest): JsonResponse { return response()->json(['data'=>$invoiceRequest->load('staff')]); }
    public function update(Request $request, InvoiceRequest $invoiceRequest): JsonResponse {
        abort_if($invoiceRequest->status!=='PENDING',422,'只能修改待審中的請款單。');
        $invoiceRequest->update($request->validate(['title'=>'sometimes|required|string|max:200','amount'=>'sometimes|required|numeric|min:0','description'=>'nullable|string']));
        return response()->json(['data'=>$invoiceRequest->fresh()]);
    }
    public function destroy(InvoiceRequest $invoiceRequest): JsonResponse {
        abort_if($invoiceRequest->status!=='PENDING',422,'只能取消待審中的請款單。');
        $invoiceRequest->update(['status'=>'CANCELLED']);
        return response()->json(null,204);
    }
}
