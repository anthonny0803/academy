<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

// Initial route
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group routes that require authentication
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // User management routes
    Route::middleware([RoleMiddleware::class . ':SuperAdmin|Administrador'])->group(function () {

        // User routes
        Route::controller(UserController::class)->group(function () {
            Route::get('users/index', 'index')->name('users.index');
            Route::get('users/{user}/show', 'show')->name('users.show');
            Route::get('users/create', 'create')->name('users.create');
            Route::post('users/create', 'store')->name('users.store');
            Route::get('users/{user}/edit', 'edit')->name('users.edit');
            Route::patch('users/{user}/edit', 'update')->name('users.update');
            Route::delete('users/{user}/delete', 'destroy')->name('users.destroy');
            Route::patch('/users/{user}/toggle', 'toggleActivation')->name('users.toggle');
        });

        // Representative routes
        Route::controller(RepresentativeController::class)->group(function () {
            Route::get('representatives/index', 'index')->name('representatives.index');
            Route::get('representatives/{representative}/show', 'show')->name('representatives.show');
            Route::get('representatives/create', 'create')->name('representatives.create');
            Route::post('representatives/create', 'store')->name('representatives.store');
        });

        // Student routes
        Route::controller(StudentController::class)->group(function () {
            Route::get('students/{representative}/create', 'create')->name('students.create');
            Route::post('students/create', 'store')->name('students.store');
        });
    });
});

require __DIR__ . '/auth.php';
