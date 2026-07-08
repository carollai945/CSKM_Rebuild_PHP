<?php

namespace App\Http\Controllers;

use App\Jobs\ImportLeadsJob;
use App\Models\ImportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * F04 電訪名單匯入
 *
 * 功能編號：F04
 * 對應文件：docs/sdd/f04-lead-import-sdd.md
 */
class LeadImportController extends Controller
{
    public function import(Request $request): JsonResponse
    {
        Gate::authorize('management');

        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $path = $validated['file']->store('imports', 'local');

        $importJob = ImportJob::create([
            'status' => ImportJob::STATUS_PENDING,
            'total' => 0,
            'processed' => 0,
            'errors' => [],
        ]);

        ImportLeadsJob::dispatch($importJob->id, $path);

        return response()->json(['job_id' => $importJob->id], 202);
    }

    public function show(ImportJob $importJob): JsonResponse
    {
        Gate::authorize('management');

        return response()->json([
            'data' => [
                'id' => $importJob->id,
                'status' => $importJob->status,
                'total' => $importJob->total,
                'processed' => $importJob->processed,
                'errors' => $importJob->errors ?? [],
            ],
        ]);
    }
}
