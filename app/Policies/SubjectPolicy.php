<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubjectPolicy
{
    public function viewAny(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor']) || $currentUser->id === 1
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de asignaturas.');
    }
}
