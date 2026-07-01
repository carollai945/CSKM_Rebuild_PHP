<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->user();

        $query = Subject::query()->orderBy('id');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $request->user();

        $subject = Subject::create($request->validated());

        return response()->json(['data' => $subject], 201);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): JsonResponse
    {
        $request->user();

        $subject->update($request->validated());

        return response()->json(['data' => $subject->fresh()]);
    }

    public function destroy(Request $request, Subject $subject): JsonResponse
    {
        $request->user();

        $subject->delete();

        return response()->json(null, 204);
    }
}
