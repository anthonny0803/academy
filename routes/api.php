<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['public.token', 'throttle:10,1'])->prefix('public')->group(function () {
    // Endpoints se añadirán en siguiente slice
});