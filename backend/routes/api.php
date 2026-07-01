<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\TitleController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'check']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/ping', fn () => response()->json(['status' => 'ok']));
});

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    Route::prefix('master')->group(function () {
        Route::get('/regions', [RegionController::class, 'index']);

        Route::middleware(['auth:sanctum', 'role:admin,ceo'])->group(function () {
            Route::post('/regions', [RegionController::class, 'store']);
            Route::put('/regions/{region}', [RegionController::class, 'update']);
            Route::delete('/regions/{region}', [RegionController::class, 'destroy']);
        });

        Route::apiResource('departments', DepartmentController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('titles', TitleController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});
