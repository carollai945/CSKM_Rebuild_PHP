<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfessorRequest;
use App\Http\Requests\UpdateProfessorRequest;
use App\Models\Professor;
use App\Models\ProfessorFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Professor::with('files')->orderBy('id');

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show(Professor $professor): JsonResponse
    {
        return response()->json(['data' => $professor->load('files')]);
    }

    public function store(StoreProfessorRequest $request): JsonResponse
    {
        $professor = Professor::create($request->safe()->except('document_file_names'));

        if ($request->filled('document_file_names')) {
            foreach ($request->document_file_names as $fileName) {
                $professor->files()->create([
                    'file_name' => $fileName,
                    'file_path' => 'C:/CSKM/Other/C01/' . $fileName,
                ]);
            }
        }

        return response()->json(['data' => $professor->load('files')], 201);
    }

    public function update(UpdateProfessorRequest $request, Professor $professor): JsonResponse
    {
        $professor->update($request->safe()->except('document_file_names'));

        if ($request->has('document_file_names')) {
            $professor->files()->delete();
            foreach ($request->document_file_names ?? [] as $fileName) {
                $professor->files()->create([
                    'file_name' => $fileName,
                    'file_path' => 'C:/CSKM/Other/C01/' . $fileName,
                ]);
            }
        }

        return response()->json(['data' => $professor->load('files')]);
    }

    public function destroy(Professor $professor): JsonResponse
    {
        $professor->files()->delete();
        $professor->delete();

        return response()->json(null, 204);
    }

    public function destroyFile(Professor $professor, ProfessorFile $file): JsonResponse
    {
        if ($file->professor_id !== $professor->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $file->delete();

        return response()->json(null, 204);
    }
}
