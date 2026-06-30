<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreRegionRequest;
use App\Http\Requests\MasterData\UpdateRegionRequest;
use App\Http\Resources\MasterData\RegionResource;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $regions = Region::orderBy('code')->get();

        return RegionResource::collection($regions);
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        $region = Region::create($request->validated());

        return response()->json(new RegionResource($region), 201);
    }

    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $region->update($request->validated());

        return response()->json(new RegionResource($region));
    }

    public function destroy(Region $region): JsonResponse
    {
        $region->delete();

        return response()->json(['message' => '已刪除'], 200);
    }
}
