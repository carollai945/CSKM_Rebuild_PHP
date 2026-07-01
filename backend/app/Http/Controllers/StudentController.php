<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['region', 'advisor'])->orderBy('student_no');

        if ($request->filled('keyword')) {
            $kw = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', $kw)
                  ->orWhere('student_no', 'like', $kw)
                  ->orWhere('phone', 'like', $kw)
                  ->orWhere('mobile', 'like', $kw);
            });
        }
        if ($request->filled('region_id'))        $query->where('region_id', $request->region_id);
        if ($request->filled('status'))           $query->where('status', $request->status);
        if ($request->filled('advisor_staff_id')) $query->where('advisor_staff_id', $request->advisor_staff_id);

        return response()->json(['data' => $query->get()]);
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json(['data' => $student->load(['region', 'advisor', 'studentCourses.course'])]);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = Student::create($request->validated());

        return response()->json(['data' => $student->load(['region', 'advisor'])], 201);
    }

    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $student->update($request->validated());

        return response()->json(['data' => $student->load(['region', 'advisor'])]);
    }

    public function updateAdvisor(Request $request, Student $student): JsonResponse
    {
        $request->validate(['advisor_staff_id' => 'required|exists:staff,id']);
        $student->update(['advisor_staff_id' => $request->advisor_staff_id]);

        return response()->json(['data' => $student->load(['region', 'advisor'])]);
    }

    public function getCourses(Student $student): JsonResponse
    {
        return response()->json(['data' => $student->studentCourses()->with('course')->get()]);
    }

    public function updateCourses(Request $request, Student $student): JsonResponse
    {
        $request->validate([
            'courses'              => 'required|array',
            'courses.*.course_id'  => 'required|exists:courses,id',
            'courses.*.status'     => 'nullable|in:ENROLLED,COMPLETED,DROPPED',
            'courses.*.joined_at'  => 'nullable|date',
            'courses.*.finished_at'=> 'nullable|date',
        ]);

        foreach ($request->courses as $courseData) {
            StudentCourse::updateOrCreate(
                ['student_id' => $student->id, 'course_id' => $courseData['course_id']],
                array_filter([
                    'status'      => $courseData['status'] ?? 'ENROLLED',
                    'joined_at'   => $courseData['joined_at'] ?? null,
                    'finished_at' => $courseData['finished_at'] ?? null,
                ], fn($v) => $v !== null)
            );
        }

        return response()->json(['data' => $student->studentCourses()->with('course')->get()]);
    }
}
