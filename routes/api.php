<?php

use App\Domains\Academics\Http\Controllers\Api\AcademicPeriodController;
use App\Domains\Academics\Http\Controllers\Api\SectionController;
use App\Domains\Academics\Http\Controllers\Api\SubjectController;
use App\Domains\Academics\Http\Controllers\Api\TeacherController;
use App\Domains\Grades\Http\Controllers\Api\PublicGradesController;
use App\Domains\Identity\Http\Controllers\Api\AuthController;
use App\Domains\Identity\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/token', [AuthController::class, 'token'])
        ->middleware('throttle:6,1')
        ->name('api.v1.auth.token');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])
            ->name('api.v1.auth.logout');
    });

    Route::middleware(['auth:sanctum', 'throttle:api'])->name('api.v1.')->group(function () {
        Route::apiResource('academic-periods', AcademicPeriodController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('teachers', TeacherController::class)->except(['destroy']);
        Route::apiResource('users', UserController::class);
    });
});

Route::middleware(['public.token', 'throttle:10,1'])->prefix('public')->group(function () {
    Route::get('student/grades', [PublicGradesController::class, 'studentGrades'])
        ->name('api.public.student.grades');

    Route::get('representative/grades', [PublicGradesController::class, 'representativeGrades'])
        ->name('api.public.representative.grades');
});
