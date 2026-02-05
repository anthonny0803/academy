<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectTeacherController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SectionSubjectTeacherController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeColumnController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\RoleManagementController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

/*Public Routes*/

Route::get('/', fn() => view('auth.login'));
Route::get('/health', fn() => response('OK', 200));

/*Authenticated Routes*/

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin Routes (Supervisor | Administrador)
    Route::middleware(RoleMiddleware::class . ':Supervisor|Administrador')->group(function () {

        // Users
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleActivation'])
            ->name('users.toggle');

        // Teachers
        Route::resource('teachers', TeacherController::class);
        Route::patch('teachers/{teacher}/toggle', [TeacherController::class, 'toggleActivation'])
            ->name('teachers.toggle');

        // Subject-Teacher (Subject Assignment to Teachers)
        Route::get('subject-teacher', [SubjectTeacherController::class, 'index'])
            ->name('subject-teacher.index');
        Route::get('teachers/{teacher}/subjects/assign', [SubjectTeacherController::class, 'assign'])
            ->name('teachers.subjects.assign');
        Route::post('teachers/{teacher}/subjects', [SubjectTeacherController::class, 'store'])
            ->name('teachers.subjects.store');
        Route::delete('teachers/{teacher}/subjects/{subject}', [SubjectTeacherController::class, 'destroy'])
            ->name('teachers.subjects.destroy');

        // Representatives
        Route::resource('representatives', RepresentativeController::class);

        // Students
        Route::resource('representatives.students', StudentController::class)
            ->shallow()
            ->only(['create', 'store']);
        Route::resource('students', StudentController::class)
            ->only(['index', 'show', 'edit', 'update']);
        Route::get('students/{student}/reassign-representative', [StudentController::class, 'showReassignForm'])
            ->name('students.reassign-representative.form');
        Route::patch('students/{student}/reassign-representative', [StudentController::class, 'reassignRepresentative'])
            ->name('students.reassign-representative');
        Route::get('students/{student}/convert-to-self-represented', [StudentController::class, 'showConvertToSelfRepresentedForm'])
            ->name('students.convert-to-self-represented.form');
        Route::patch('students/{student}/convert-to-self-represented', [StudentController::class, 'convertToSelfRepresented'])
            ->name('students.convert-to-self-represented');
        Route::patch('students/{student}/situation', [StudentController::class, 'changeSituation'])
            ->name('students.situation');
        Route::get('students/{student}/withdraw', [StudentController::class, 'showWithdrawForm'])
            ->name('students.withdraw.form');
        Route::patch('students/{student}/withdraw', [StudentController::class, 'withdraw'])
            ->name('students.withdraw');

        // Subjects
        Route::resource('subjects', SubjectController::class)->except(['show']);
        Route::patch('subjects/{subject}/toggle', [SubjectController::class, 'toggleActivation'])
            ->name('subjects.toggle');

        // Academic Periods
        Route::resource('academic-periods', AcademicPeriodController::class);
        Route::patch('academic-periods/{academicPeriod}/close', [AcademicPeriodController::class, 'close'])
            ->name('academic-periods.close');

        // Sections
        Route::get('sections/{section}/assignments', [SectionController::class, 'assignments'])
            ->name('sections.assignments');
        Route::resource('sections', SectionController::class);
        Route::patch('sections/{section}/toggle', [SectionController::class, 'toggleActivation'])
            ->name('sections.toggle');

        // Section-Subject-Teacher (Subject-Teacher Assignment within Sections)
        Route::resource('section-subject-teacher', SectionSubjectTeacherController::class)
            ->except(['index', 'show', 'create', 'edit']);

        // Enrollments
        Route::resource('enrollments', EnrollmentController::class)
            ->only(['index', 'show', 'destroy']);
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

        // Role Management
        Route::get('role-management', [RoleManagementController::class, 'index'])
            ->name('role-management.index');
        Route::get('role-management/{user}/assign', [RoleManagementController::class, 'showAssignOptions'])
            ->name('role-management.show-assign-options');
        Route::get('role-management/{user}/assign/{role}', [RoleManagementController::class, 'showForm'])
            ->name('role-management.show-form');
        Route::post('role-management/{user}/assign/{role}', [RoleManagementController::class, 'assign'])
            ->name('role-management.assign');
    });

    // Teacher Routes (Grades)
    Route::get('my-assignments', [GradeController::class, 'teacherAssignments'])
        ->name('teacher.assignments');

    // Grade Columns
    Route::get('section-subject-teacher/{sectionSubjectTeacher}/grade-columns', [GradeColumnController::class, 'index'])
        ->name('grade-columns.index');
    Route::post('section-subject-teacher/{sectionSubjectTeacher}/grade-columns', [GradeColumnController::class, 'store'])
        ->name('grade-columns.store');
    Route::put('grade-columns/{gradeColumn}', [GradeColumnController::class, 'update'])
        ->name('grade-columns.update');
    Route::delete('grade-columns/{gradeColumn}', [GradeColumnController::class, 'destroy'])
        ->name('grade-columns.destroy');

    // Grades
    Route::get('section-subject-teacher/{sectionSubjectTeacher}/grades', [GradeController::class, 'index'])
        ->name('grades.index');
    Route::post('grade-columns/{gradeColumn}/grades', [GradeController::class, 'store'])
        ->name('grades.store');
    Route::put('grades/{grade}', [GradeController::class, 'update'])
        ->name('grades.update');
    Route::delete('grades/{grade}', [GradeController::class, 'destroy'])
        ->name('grades.destroy');
});

require __DIR__ . '/auth.php';