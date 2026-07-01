<?php

use App\Http\Controllers\Academic\CourseController;
use App\Http\Controllers\Academic\InstituteController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\FeeItemController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\TitleController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
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

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/me/change-password', [PasswordController::class, 'change']);

        Route::get('/classrooms', [ClassroomController::class, 'index']);
        Route::middleware('role:admin,ceo')->group(function () {
            Route::post('/classrooms', [ClassroomController::class, 'store']);
            Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update']);
            Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy']);
        });

        Route::get('/staff/autocomplete', [StaffController::class, 'autocomplete']);
        Route::get('/staff/overview', [StaffController::class, 'overview']);
        Route::get('/staff', [StaffController::class, 'index']);
        Route::get('/staff/{staff}', [StaffController::class, 'show']);
        Route::middleware('role:admin')->group(function () {
            Route::post('/staff', [StaffController::class, 'store']);
            Route::put('/staff/{staff}', [StaffController::class, 'update']);
            Route::patch('/staff/{staff}/status', [StaffController::class, 'updateStatus']);
        });

        Route::get('/institutes', [InstituteController::class, 'index']);
        Route::get('/institutes/{institute}', [InstituteController::class, 'show']);
        Route::middleware('role:admin,ceo')->group(function () {
            Route::post('/institutes', [InstituteController::class, 'store']);
            Route::put('/institutes/{institute}', [InstituteController::class, 'update']);
            Route::delete('/institutes/{institute}', [InstituteController::class, 'destroy']);
        });

        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/courses/{course}', [CourseController::class, 'show']);
        Route::middleware('role:admin,ceo')->group(function () {
            Route::post('/courses', [CourseController::class, 'store']);
            Route::put('/courses/{course}', [CourseController::class, 'update']);
            Route::delete('/courses/{course}', [CourseController::class, 'destroy']);
        });

        Route::get('/subjects', [SubjectController::class, 'index']);
        Route::get('/subjects/{subject}', [SubjectController::class, 'show']);
        Route::middleware('role:admin,ceo')->group(function () {
            Route::post('/subjects', [SubjectController::class, 'store']);
            Route::put('/subjects/{subject}', [SubjectController::class, 'update']);
            Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy']);
        });

        Route::get('/professors', [ProfessorController::class, 'index']);
        Route::get('/professors/{professor}', [ProfessorController::class, 'show']);
        Route::middleware('role:admin,ceo')->group(function () {
            Route::post('/professors', [ProfessorController::class, 'store']);
            Route::put('/professors/{professor}', [ProfessorController::class, 'update']);
            Route::delete('/professors/{professor}', [ProfessorController::class, 'destroy']);
            Route::delete('/professors/{professor}/files/{file}', [ProfessorController::class, 'destroyFile']);
        });

        Route::get('/leads', [LeadController::class, 'index']);
        Route::post('/leads/assign', [LeadController::class, 'assign']);
        Route::post('/leads', [LeadController::class, 'store']);
        Route::put('/leads/{lead}', [LeadController::class, 'update']);
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy']);

        Route::get('/interviews', [InterviewController::class, 'index']);
        Route::get('/interviews/{interview}', [InterviewController::class, 'show']);
        Route::post('/interviews', [InterviewController::class, 'store']);
        Route::put('/interviews/{interview}', [InterviewController::class, 'update']);
        Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy']);

        Route::get('/fee-items', [FeeItemController::class, 'index']);
        Route::middleware('role:admin,ceo')->group(function () {
            Route::post('/fee-items', [FeeItemController::class, 'store']);
            Route::put('/fee-items/{feeItem}', [FeeItemController::class, 'update']);
            Route::delete('/fee-items/{feeItem}', [FeeItemController::class, 'destroy']);
        });

        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/students', [StudentController::class, 'store']);
        Route::get('/students/{student}', [StudentController::class, 'show']);
        Route::put('/students/{student}', [StudentController::class, 'update']);
        Route::patch('/students/{student}/advisor', [StudentController::class, 'updateAdvisor']);
        Route::get('/students/{student}/courses', [StudentController::class, 'getCourses']);
        Route::put('/students/{student}/courses', [StudentController::class, 'updateCourses']);
    });
});
