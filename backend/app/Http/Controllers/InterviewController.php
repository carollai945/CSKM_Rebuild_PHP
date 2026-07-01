<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Models\InterviewRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InterviewRecord::with(['lead', 'staff'])->orderBy('interview_date', 'desc');

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('result_code')) {
            $query->where('result_code', $request->result_code);
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
