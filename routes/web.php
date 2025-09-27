<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
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
        Route::resource('representatives', RepresentativeController::class);
        Route::patch('representatives/{representative}/toggle', [RepresentativeController::class, 'toggleActivation'])
            ->name('representatives.toggle');
        Route::resource('representatives.students', StudentController::class)
            ->shallow()
            ->only(['create', 'store']);
        Route::resource('subjects', SubjectController::class)->except(['show']);
    });
});

require __DIR__ . '/auth.php';
