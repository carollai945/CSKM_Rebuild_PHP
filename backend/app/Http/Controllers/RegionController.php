<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    public function index(): JsonResponse
    {
        $regions = Region::all();

        return response()->json(['data' => $regions]);
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        $region = Region::create(array_merge(
            $request->validated(),
            ['created_by' => $request->user()?->id]
        ));

        return response()->json(['data' => $region], 201);
    }

    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $region->update(array_merge(
            $request->validated(),
            ['updated_by' => $request->user()?->id]
        ));

        return response()->json(['data' => $region]);
    }

    public function destroy(Region $region): JsonResponse
    {
        $region->delete();

        return response()->json(null, 204);
    }
}
