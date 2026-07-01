<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = Department::orderBy('department_no')->get();

        return response()->json(['data' => $departments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'region_id'       => ['nullable', 'integer'],
            'department_no'   => ['required', 'string', 'max:50', 'unique:departments,department_no'],
            'department_name' => ['required', 'string', 'max:100'],
            'status'          => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ]);

        $department = Department::create($validated);

        return response()->json(['data' => $department], 201);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'region_id'       => ['nullable', 'integer'],
            'department_no'   => ['sometimes', 'string', 'max:50', Rule::unique('departments', 'department_no')->ignore($department->id)],
            'department_name' => ['sometimes', 'string', 'max:100'],
            'status'          => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ]);

        if (isset($validated['status']) && $validated['status'] === 'inactive') {
            if ($department->titles()->exists()) {
                return response()->json([
                    'error' => [
                        'code'    => 'DEPARTMENT_REFERENCED',
                        'message' => '該部門已被職稱引用，不得停用',
                    ],
                ], 422);
            }
        }

        $department->update($validated);

        return response()->json(['data' => $department]);
    }

    public function destroy(Department $department): JsonResponse
    {
        if ($department->titles()->exists()) {
            return response()->json([
                'error' => [
                    'code'    => 'DEPARTMENT_REFERENCED',
                    'message' => '該部門已被職稱引用，不得刪除',
                ],
            ], 422);
        }

        $department->delete();

        return response()->json(null, 204);
    }
}
