<?php

namespace App\Http\Controllers;

use App\Models\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StudentService::with(['student', 'staff'])
            ->when($request->student_id, fn ($q, $v) => $q->where('student_id', $v))
            ->when($request->staff_id, fn ($q, $v) => $q->where('staff_id', $v))
            ->when($request->service_type, fn ($q, $v) => $q->where('service_type', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest();

        return response()->json(['data' => $query->paginate(20)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'service_type' => 'required|string|max:50',
            'content'      => 'nullable|string',
            'status'       => 'nullable|in:OPEN,CLOSED',
            'service_date' => 'nullable|date',
        ]);

        $validated['staff_id'] = $request->user()->id;
        $service = StudentService::create($validated);

        return response()->json(['data' => $service], 201);
    }

    public function show(StudentService $studentService): JsonResponse
    {
        return response()->json(['data' => $studentService->load(['student', 'staff'])]);
    }

    public function update(Request $request, StudentService $studentService): JsonResponse
    {
        $validated = $request->validate([
            'service_type' => 'sometimes|required|string|max:50',
            'content'      => 'nullable|string',
            'status'       => 'nullable|in:OPEN,CLOSED',
            'service_date' => 'nullable|date',
        ]);

        $studentService->update($validated);

        return response()->json(['data' => $studentService->fresh()]);
    }

    public function destroy(StudentService $studentService): JsonResponse
    {
        $studentService->delete();
        return response()->json(null, 204);
    }
}
