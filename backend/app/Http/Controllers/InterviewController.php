<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Enums\Role;
use App\Models\InterviewRecord;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * B01 電訪進度追蹤
 *
 * 功能編號：B01
 * 對應文件：docs/sdd/b01-interview-sdd.md
 */
class InterviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = InterviewRecord::with(['lead', 'staff'])->orderBy('interview_date', 'desc');

        // 角色範圍：staff 只看自己的電訪紀錄
        if ($user->role === Role::Staff) {
            $staffId = Staff::where('user_id', $user->id)->value('id');
            if ($staffId) {
                $query->where('staff_id', $staffId);
            }
        }

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        if ($request->filled('staff_id') && $user->role !== Role::Staff) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('result_code')) {
            $query->where('result_code', $request->result_code);
        }

        if ($request->filled('from')) {
            $query->where('interview_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('interview_date', '<=', $request->to);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(InterviewRecord $interview): JsonResponse
    {
        return response()->json(['data' => $interview->load(['lead', 'staff'])]);
    }

    public function store(StoreInterviewRequest $request): JsonResponse
    {
        $record = InterviewRecord::create($request->validated());

        return response()->json(['data' => $record->load(['lead', 'staff'])], 201);
    }

    public function update(UpdateInterviewRequest $request, InterviewRecord $interview): JsonResponse
    {
        $interview->update($request->validated());

        return response()->json(['data' => $interview->load(['lead', 'staff'])]);
    }

    public function destroy(InterviewRecord $interview): JsonResponse
    {
        $interview->delete();

        return response()->json(null, 204);
    }
}
