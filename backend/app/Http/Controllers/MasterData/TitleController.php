<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreTitleRequest;
use App\Http\Requests\MasterData\UpdateTitleRequest;
use App\Http\Resources\MasterData\TitleResource;
use App\Models\Title;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TitleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TitleResource::collection(Title::orderBy('code')->get());
    }

    public function store(StoreTitleRequest $request): JsonResponse
    {
        return response()->json(new TitleResource(Title::create($request->validated())), 201);
    }

    public function update(UpdateTitleRequest $request, Title $title): JsonResponse
    {
        $title->update($request->validated());
        return response()->json(new TitleResource($title));
    }

    public function destroy(Title $title): JsonResponse
    {
        $title->delete();
        return response()->json(['message' => '已刪除']);
    }
}
