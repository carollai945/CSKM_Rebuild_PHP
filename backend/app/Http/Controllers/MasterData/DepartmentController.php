<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreDepartmentRequest;
use App\Http\Requests\MasterData\UpdateDepartmentRequest;
use App\Http\Resources\MasterData\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepartmentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DepartmentResource::collection(Department::orderBy('code')->get());
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        return response()->json(new DepartmentResource(Department::create($request->validated())), 201);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        $department->update($request->validated());
        return response()->json(new DepartmentResource($department));
    }

    public function destroy(Department $department): JsonResponse
    {
        $department->delete();
        return response()->json(['message' => '已刪除']);
    }
}
