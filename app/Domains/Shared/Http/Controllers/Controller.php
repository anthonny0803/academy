<?php

namespace App\Domains\Shared\Http\Controllers;

use App\Domains\Identity\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function currentUser(): User
    {
        return Auth::user();
    }
}
