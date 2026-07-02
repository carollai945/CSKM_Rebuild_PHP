<?php
namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
class SystemBackupController extends Controller {
    public function index(): JsonResponse {
        Gate::authorize('is-admin');
        $backupPath = storage_path('app/backups');
        $files = [];
        if (is_dir($backupPath)) {
            foreach (scandir($backupPath) as $file) {
                if ($file !== '.' && $file !== '..') {
                    $files[] = ['name'=>$file,'size'=>filesize("$backupPath/$file"),'created_at'=>date('Y-m-d H:i:s',filemtime("$backupPath/$file"))];
                }
            }
        }
        return response()->json(['data'=>$files]);
    }
    public function store(Request $request): JsonResponse {
        Gate::authorize('is-admin');
        // Simulate backup creation
        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) mkdir($backupPath, 0755, true);
        $filename = 'backup_'.date('Y-m-d_H-i-s').'.sql';
        file_put_contents("$backupPath/$filename", "-- CSKM DB Backup\n-- Generated: ".date('Y-m-d H:i:s')."\n");
        return response()->json(['message'=>'備份已建立。','filename'=>$filename],201);
    }
}
