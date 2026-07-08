<?php
namespace App\Http\Controllers;
use App\Models\ApprovalActionLog;
use App\Models\InvoiceRequest;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
/**
 * A05 請款申請
 *
 * 功能編號：A05
 * 對應文件：docs/sdd/a05-invoice-request-sdd.md
 */
class InvoiceRequestController extends Controller {
    private function myStaffId(Request $r): ?int { return Staff::where('user_id',$r->user()->id)->value('id'); }
    public function index(Request $request): JsonResponse {
        $q = InvoiceRequest::where('staff_id',$this->myStaffId($request))->when($request->status,fn($q,$v)=>$q->where('status',$v))->latest();
        return response()->json(['data'=>$q->paginate(20)]);
    }
    public function store(Request $request): JsonResponse {
        $v = $request->validate(['title'=>'required|string|max:200','amount'=>'required|numeric|min:0','description'=>'nullable|string']);
        $v['staff_id']=$this->myStaffId($request);
        $invoiceRequest = InvoiceRequest::create($v);
        ApprovalActionLog::create(['related_type'=>'invoice_request','related_id'=>$invoiceRequest->id,'actor_id'=>$request->user()->id,'action'=>'SUBMIT']);
        return response()->json(['data'=>$invoiceRequest],201);
    }
    public function show(InvoiceRequest $invoiceRequest): JsonResponse { return response()->json(['data'=>$invoiceRequest->load('staff')]); }
    public function update(Request $request, InvoiceRequest $invoiceRequest): JsonResponse {
        abort_if($invoiceRequest->status!=='DRAFT',422,'只能修改草稿狀態的請款單。');
        $invoiceRequest->update($request->validate(['title'=>'sometimes|required|string|max:200','amount'=>'sometimes|required|numeric|min:0','description'=>'nullable|string']));
        return response()->json(['data'=>$invoiceRequest->fresh()]);
    }
    public function destroy(InvoiceRequest $invoiceRequest): JsonResponse {
        abort_if($invoiceRequest->status!=='DRAFT',422,'只能刪除草稿狀態的請款單。');
        $invoiceRequest->delete();
        return response()->json(null,204);
    }
    public function submit(Request $request, InvoiceRequest $invoiceRequest): JsonResponse {
        abort_if($invoiceRequest->status!=='DRAFT',422,'只有草稿狀態的請款單可以送審。');
        $invoiceRequest->update(['status'=>'PENDING']);
        return response()->json(['data'=>$invoiceRequest->fresh()]);
    }
    public function cancel(Request $request, InvoiceRequest $invoiceRequest): JsonResponse {
        abort_if($invoiceRequest->staff_id!==$this->myStaffId($request),403,'無權限取消此請款單。');
        abort_if($invoiceRequest->status!=='PENDING',422,'只有待審狀態的請款單可以取消。');
        $invoiceRequest->update(['status'=>'CANCELLED']);
        return response()->json(['data'=>$invoiceRequest->fresh()]);
    }
}
