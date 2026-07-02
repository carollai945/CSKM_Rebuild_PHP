<?php
namespace App\Http\Controllers;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class LeadImportController extends Controller {
    public function import(Request $request): JsonResponse {
        Gate::authorize('management');
        $request->validate(['file'=>'required|file|mimes:xlsx,xls,csv|max:10240']);
        // Parse CSV/Excel content (simplified for now - real implementation uses phpspreadsheet)
        $file = $request->file('file');
        $lines = array_filter(explode("\n", file_get_contents($file->getRealPath())));
        $imported = 0;
        $errors = [];
        foreach (array_slice($lines, 1) as $idx => $line) { // skip header
            $cols = str_getcsv($line);
            if (count($cols) < 2) continue;
            try {
                Lead::create(['name'=>trim($cols[0]),'phone'=>trim($cols[1]),'source_code'=>trim($cols[2]??'IMPORT')]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "第 ".($idx+2)." 列：{$e->getMessage()}";
            }
        }
        return response()->json(['message'=>"匯入完成，共 {$imported} 筆。",'imported'=>$imported,'errors'=>$errors],201);
    }
}
