<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Models\Classroom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->user();

        $query = Classroom::query()->orderBy('id');

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->integer('region_id'));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreClassroomRequest $request): JsonResponse
    {
        $request->user();

        $classroom = Classroom::create($request->validated());

        return response()->json(['data' => $classroom], 201);
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): JsonResponse
    {
        $request->user();

        $classroom->update($request->validated());

        return response()->json(['data' => $classroom->fresh()]);
    }

    public function destroy(Request $request, Classroom $classroom): JsonResponse
    {
        $request->user();

        $classroom->delete();

        return response()->json(null, 204);
    }
}
