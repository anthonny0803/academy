<?php

use App\Http\Controllers\Api\PublicGradesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['public.token', 'throttle:10,1'])->prefix('public')->group(function () {
    Route::get('student/grades', [PublicGradesController::class, 'studentGrades'])
        ->name('api.public.student.grades');

    Route::get('representative/grades', [PublicGradesController::class, 'representativeGrades'])
        ->name('api.public.representative.grades');
});