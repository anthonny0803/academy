<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

// Ruta inicial
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rutas de perfil de Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware([RoleMiddleware::class . ':SuperAdmin|Administrador'])->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('users/create', 'create')->name('users.create');
            Route::post('users/create', 'store')->name('users.store');
        });

        Route::controller(ClientController::class)->group(function () {
            Route::get('clients/create', 'create')->name('clients.create');
            Route::post('clients/create', 'store')->name('clients.store');
        });

        Route::controller(StudentController::class)->group(function () {
            Route::get('students/{representative}/create', 'create')->name('students.create');
            Route::post('students/create', 'store')->name('students.store');
        });
    });
});

require __DIR__ . '/auth.php';
