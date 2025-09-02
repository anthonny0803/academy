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

    // Rutas de administración de usuarios
    Route::middleware([RoleMiddleware::class . ':SuperAdmin|Administrador'])->group(function () {

        // Rutas del usuario
        Route::controller(UserController::class)->group(function () {
            Route::get('users/index', 'index')->name('users.index');
            Route::get('users/{user}/show', 'show')->name('users.show');
            Route::get('users/create', 'create')->name('users.create');
            Route::post('users/create', 'store')->name('users.store');
            Route::get('users/{user}/edit', 'edit')->name('users.edit');
            Route::patch('users/{user}/edit', 'update')->name('users.update');
            Route::delete('users/{user}/delete', 'destroy')->name('users.destroy');
        });

        // Rutas del cliente
        Route::controller(ClientController::class)->group(function () {
            Route::get('clients/create', 'create')->name('clients.create');
            Route::post('clients/create', 'store')->name('clients.store');
        });

        // Rutas del estudiante
        Route::controller(StudentController::class)->group(function () {
            Route::get('students/{representative}/create', 'create')->name('students.create');
            Route::post('students/create', 'store')->name('students.store');
        });
    });
});

require __DIR__ . '/auth.php';
