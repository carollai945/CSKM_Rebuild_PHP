<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\MasterData\DepartmentController;
use App\Http\Controllers\MasterData\RegionController;
use App\Http\Controllers\MasterData\TitleController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'check']);

Route::prefix('v1')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // Master Data
    Route::prefix('master')->middleware('auth:sanctum')->group(function () {
        // Read: all authenticated roles
        Route::get('/regions', [RegionController::class, 'index']);
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/titles', [TitleController::class, 'index']);

        // Write: CEO / ADMIN only
        Route::middleware('role:CEO,ADMIN')->group(function () {
            Route::post('/regions', [RegionController::class, 'store']);
            Route::put('/regions/{region}', [RegionController::class, 'update']);
            Route::delete('/regions/{region}', [RegionController::class, 'destroy']);

            Route::post('/departments', [DepartmentController::class, 'store']);
            Route::put('/departments/{department}', [DepartmentController::class, 'update']);
            Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);

            Route::post('/titles', [TitleController::class, 'store']);
            Route::put('/titles/{title}', [TitleController::class, 'update']);
            Route::delete('/titles/{title}', [TitleController::class, 'destroy']);
        });
    });
});
