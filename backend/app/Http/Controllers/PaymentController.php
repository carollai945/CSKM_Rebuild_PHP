<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function index(Request $request): JsonResponse {
        $query = Payment::with(['student','feeItem'])
            ->when($request->student_id, fn($q,$v) => $q->where('student_id',$v))
            ->when($request->status, fn($q,$v) => $q->where('status',$v))
            ->latest();
        return response()->json(['data' => $query->paginate(20)]);
    }

    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'student_id'     => 'required|exists:students,id',
            'fee_item_id'    => 'nullable|exists:fee_items,id',
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'payment_method' => 'nullable|string|max:30',
            'payment_date'   => 'nullable|date',
            'note'           => 'nullable|string',
        ]);
        $payment = Payment::create($validated);
        return response()->json(['data' => $payment], 201);
    }

    public function show(Payment $payment): JsonResponse {
        return response()->json(['data' => $payment->load(['student','feeItem'])]);
    }

    public function financeConfirm(Request $request, Payment $payment): JsonResponse {
        abort_if($payment->status !== 'PENDING', 422, '只能確認待審狀態的繳費記錄。');
        $staffId = Staff::where('user_id', $request->user()->id)->value('id');
        $payment->update(['status'=>'FINANCE_CONFIRMED','finance_confirmed_by'=>$staffId]);
        return response()->json(['data' => $payment->fresh()]);
    }

    public function academicConfirm(Request $request, Payment $payment): JsonResponse {
        abort_if($payment->status !== 'FINANCE_CONFIRMED', 422, '請先完成財務確認。');
        $staffId = Staff::where('user_id', $request->user()->id)->value('id');
        $payment->update(['status'=>'ACADEMIC_CONFIRMED','academic_confirmed_by'=>$staffId]);
        return response()->json(['data' => $payment->fresh()]);
    }

    public function reject(Request $request, Payment $payment): JsonResponse {
        abort_if(!in_array($payment->status,['PENDING','FINANCE_CONFIRMED']), 422, '無法退回此狀態的繳費記錄。');
        $validated = $request->validate(['note'=>'nullable|string']);
        $payment->update(['status'=>'REJECTED','note'=>$validated['note'] ?? $payment->note]);
        return response()->json(['data' => $payment->fresh()]);
    }
}
