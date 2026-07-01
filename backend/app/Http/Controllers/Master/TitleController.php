<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TitleController extends Controller
{
    public function index(): JsonResponse
    {
        $titles = Title::with('department')->orderBy('title_no')->get();

        return response()->json(['data' => $titles]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'region_id'     => ['nullable', 'integer'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'title_no'      => [
                'required',
                'string',
                'max:50',
                Rule::unique('titles')->where(fn ($query) => $query->where('department_id', $request->input('department_id'))),
            ],
            'title_name'    => ['required', 'string', 'max:100'],
            'status'        => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ]);

        $title = Title::create($validated);

        return response()->json(['data' => $title->load('department')], 201);
    }

    public function update(Request $request, Title $title): JsonResponse
    {
        $departmentId = $request->input('department_id', $title->department_id);

        $validated = $request->validate([
            'region_id'     => ['nullable', 'integer'],
            'department_id' => ['sometimes', 'integer', 'exists:departments,id'],
            'title_no'      => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('titles')
                    ->where(fn ($query) => $query->where('department_id', $departmentId))
                    ->ignore($title->id),
            ],
            'title_name'    => ['sometimes', 'string', 'max:100'],
            'status'        => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ]);

        $title->update($validated);

        return response()->json(['data' => $title->load('department')]);
    }

    public function destroy(Title $title): JsonResponse
    {
        $title->delete();

        return response()->json(null, 204);
    }
}
