<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstituteRequest;
use App\Http\Requests\UpdateInstituteRequest;
use App\Models\Institute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstituteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->user();

        $query = Institute::query()->orderBy('id');

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->integer('region_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(StoreInstituteRequest $request): JsonResponse
    {
        $request->user();

        $institute = Institute::create($request->validated());

        return response()->json(['data' => $institute], 201);
    }

    public function update(UpdateInstituteRequest $request, Institute $institute): JsonResponse
    {
        $request->user();

        $institute->update($request->validated());

        return response()->json(['data' => $institute->fresh()]);
    }

    public function destroy(Request $request, Institute $institute): JsonResponse
    {
        $request->user();

        $institute->delete();

        return response()->json(null, 204);
    }
}
