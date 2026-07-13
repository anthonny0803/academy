<?php

use App\Domains\Academics\Http\Controllers\Api\AcademicPeriodController;
use App\Domains\Academics\Http\Controllers\Api\SectionController;
use App\Domains\Academics\Http\Controllers\Api\SubjectController;
use App\Domains\Academics\Http\Controllers\Api\TeacherController;
use App\Domains\AI\Http\Controllers\Api\StudentPerformanceObservationController;
use App\Domains\Enrollments\Http\Controllers\Api\EnrollmentController;
use App\Domains\Grades\Http\Controllers\Api\GradeController;
use App\Domains\Grades\Http\Controllers\Api\PublicGradesController;
use App\Domains\Identity\Http\Controllers\Api\AuthController;
use App\Domains\Identity\Http\Controllers\Api\UserController;
use App\Domains\Representatives\Http\Controllers\Api\RepresentativeController;
use App\Domains\Students\Http\Controllers\Api\StudentController;
use App\Domains\Tenancy\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/token', [AuthController::class, 'token'])
        ->middleware('throttle:6,1')
        ->name('api.v1.auth.token');

    Route::middleware(['auth:sanctum', 'tenant.resolve'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])
            ->name('api.v1.auth.logout');
    });

    Route::middleware(['auth:sanctum', 'tenant.resolve', 'throttle:api'])->name('api.v1.')->group(function () {
        Route::apiResource('academic-periods', AcademicPeriodController::class);
        Route::apiResource('representatives', RepresentativeController::class)->except(['destroy']);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('representatives.students', StudentController::class)->shallow()->only(['store']);
        Route::apiResource('students', StudentController::class)->only(['index', 'show', 'update']);
        Route::patch('students/{student}/withdraw', [StudentController::class, 'withdraw'])->name('students.withdraw');
        Route::post('students/{student}/performance-observations', [StudentPerformanceObservationController::class, 'store'])
            ->middleware('throttle:ai')
            ->name('students.performance-observations.store');
        Route::apiResource('students.performance-observations', StudentPerformanceObservationController::class)
            ->shallow()->only(['index', 'show']);
        Route::apiResource('students.enrollments', EnrollmentController::class)->shallow()->only(['store']);
        Route::apiResource('enrollments', EnrollmentController::class)->only(['index', 'show', 'destroy']);
        Route::patch('enrollments/{enrollment}/transfer', [EnrollmentController::class, 'transfer'])->name('enrollments.transfer');
        Route::patch('enrollments/{enrollment}/promote', [EnrollmentController::class, 'promote'])->name('enrollments.promote');
        Route::get('section-subject-teachers/{sectionSubjectTeacher}/grades', [GradeController::class, 'index'])
            ->name('section-subject-teachers.grades.index');
        Route::post('grade-columns/{gradeColumn}/grades', [GradeController::class, 'store'])
            ->name('grade-columns.grades.store');
        Route::post('grade-columns/{gradeColumn}/grades/batch', [GradeController::class, 'storeBatch'])
            ->name('grade-columns.grades.batch');
        Route::apiResource('grades', GradeController::class)->only(['show', 'update', 'destroy']);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('teachers', TeacherController::class)->except(['destroy']);
        Route::apiResource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/public-token', [TenantController::class, 'issuePublicToken'])
            ->name('tenants.public-token');
        Route::apiResource('users', UserController::class);
    });
});

Route::middleware(['throttle:10,1', 'tenant.token'])->prefix('public')->group(function () {
    Route::get('student/grades', [PublicGradesController::class, 'studentGrades'])
        ->name('api.public.student.grades');

    Route::get('representative/grades', [PublicGradesController::class, 'representativeGrades'])
        ->name('api.public.representative.grades');
});
