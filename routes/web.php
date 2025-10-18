<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\SubjectTeacherController;
use App\Http\Controllers\SectionSubjectTeacherController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::get('/', fn() => view('auth.login'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware(RoleMiddleware::class . ':Supervisor|Administrador')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActivation'])
            ->name('users.toggle');

        Route::resource('teachers', TeacherController::class);
        Route::patch('teachers/{teacher}/toggle', [TeacherController::class, 'toggleActivation'])
            ->name('teachers.toggle');

        Route::get('teachers/{teacher}/subjects/assign', [SubjectTeacherController::class, 'assign'])
            ->name('teachers.subjects.assign');
        Route::post('teachers/{teacher}/subjects', [SubjectTeacherController::class, 'store'])
            ->name('teachers.subjects.store');
        Route::delete('teachers/{teacher}/subjects/{subject}', [SubjectTeacherController::class, 'destroy'])
            ->name('teachers.subjects.destroy');

        Route::get('subject-teacher', [SubjectTeacherController::class, 'index'])
            ->name('subject-teacher.index');

        Route::resource('representatives', RepresentativeController::class);
        Route::patch('representatives/{representative}/toggle', [RepresentativeController::class, 'toggleActivation'])
            ->name('representatives.toggle');
        Route::resource('representatives.students', StudentController::class)
            ->shallow()
            ->only(['create', 'store']);

        Route::resource('students', StudentController::class)
            ->only(['index', 'show', 'edit', 'update']);
        Route::patch('students/{student}/reassign-representative', [StudentController::class, 'reassignRepresentative'])
            ->name('students.reassign-representative');
        Route::patch('students/{student}/toggle', [StudentController::class, 'toggleActivation'])
            ->name('students.toggle');

        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::patch('subjects/{subject}/toggle', [SubjectController::class, 'toggleActivation'])
            ->name('subjects.toggle');

        Route::resource('academic-periods', AcademicPeriodController::class)->except(['show']);
        Route::patch('academic-periods/{academicPeriod}/toggle', [AcademicPeriodController::class, 'toggleActivation'])
            ->name('academic-periods.toggle');

        Route::resource('sections', SectionController::class);
        Route::patch('sections/{section}/toggle', [SectionController::class, 'toggleActivation'])
            ->name('sections.toggle');

        Route::resource('section-subject-teacher', SectionSubjectTeacherController::class)
            ->except(['index', 'show', 'create', 'edit']);

        Route::resource('enrollments', EnrollmentController::class)
            ->except(['create', 'store']);
        Route::resource('students.enrollments', EnrollmentController::class)
            ->shallow()
            ->only(['create', 'store']);
        Route::get('enrollments/{enrollment}/transfer', [EnrollmentController::class, 'showTransferForm'])
            ->name('enrollments.transfer.form');
        Route::patch('enrollments/{enrollment}/transfer', [EnrollmentController::class, 'transfer'])
            ->name('enrollments.transfer');
        Route::get('enrollments/{enrollment}/promote', [EnrollmentController::class, 'showPromoteForm'])
            ->name('enrollments.promote.form');
        Route::patch('enrollments/{enrollment}/promote', [EnrollmentController::class, 'promote'])
            ->name('enrollments.promote');
    });
});

require __DIR__ . '/auth.php';
