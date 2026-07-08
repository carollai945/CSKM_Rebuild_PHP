<?php
namespace App\Http\Controllers;
use App\Models\LeaveRequest;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A03 請假申請
 *
 * 功能編號：A03
 * 對應文件：docs/sdd/a03-leave-request-sdd.md
 */
class LeaveRequestController extends Controller {
    private function myStaffId(Request $request): ?int {
        return Staff::where('user_id', $request->user()->id)->value('id');
    }

    public function index(Request $request): JsonResponse {
        $staffId = $this->myStaffId($request);
        $query = LeaveRequest::where('staff_id', $staffId)
            ->when($request->status, fn($q,$v) => $q->where('status',$v))
            ->latest();
        return response()->json(['data' => $query->paginate(20)]);
    }

    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'leave_type' => 'required|string|max:30',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after_or_equal:start_at',
            'reason'     => 'nullable|string',
        ]);
        $validated['staff_id'] = $this->myStaffId($request);
        $lr = LeaveRequest::create($validated);
        return response()->json(['data' => $lr], 201);
    }

    public function show(LeaveRequest $leaveRequest): JsonResponse {
        return response()->json(['data' => $leaveRequest->load(['staff','approver'])]);
    }

    public function update(Request $request, LeaveRequest $leaveRequest): JsonResponse {
        abort_if($leaveRequest->status !== 'DRAFT', 422, '只能修改草稿狀態的請假單。');
        $validated = $request->validate([
            'leave_type' => 'sometimes|required|string|max:30',
            'start_at'   => 'sometimes|required|date',
            'end_at'     => 'sometimes|required|date',
            'reason'     => 'nullable|string',
        ]);
        $leaveRequest->update($validated);
        return response()->json(['data' => $leaveRequest->fresh()]);
    }

    public function destroy(LeaveRequest $leaveRequest): JsonResponse {
        abort_if($leaveRequest->status !== 'DRAFT', 422, '只能刪除草稿狀態的請假單。');
        $leaveRequest->delete();
        return response()->json(null, 204);
    }

    public function submit(Request $request, LeaveRequest $leaveRequest): JsonResponse {
        abort_if($leaveRequest->status !== 'DRAFT', 422, '只有草稿狀態的請假單可以送審。');
        $leaveRequest->update(['status' => 'PENDING']);
        return response()->json(['data' => $leaveRequest->fresh()]);
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest): JsonResponse {
        $staffId = $this->myStaffId($request);
        abort_if($leaveRequest->staff_id !== $staffId, 403, '無權限取消此請假單。');
        abort_if($leaveRequest->status !== 'PENDING', 422, '只有待審狀態的請假單可以取消。');
        $leaveRequest->update(['status' => 'CANCELLED']);
        return response()->json(['data' => $leaveRequest->fresh()]);
    }
}
