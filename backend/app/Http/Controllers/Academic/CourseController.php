<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->user();

        $query = Course::query()->orderBy('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $request->user();

        $course = Course::create($request->validated());

        return response()->json(['data' => $course], 201);
    }

    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        $request->user();

        $course->update($request->validated());

        return response()->json(['data' => $course->fresh()]);
    }

    public function destroy(Request $request, Course $course): JsonResponse
    {
        $request->user();

        $course->delete();

        return response()->json(null, 204);
    }
}
