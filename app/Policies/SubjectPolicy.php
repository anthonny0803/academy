<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubjectPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param User $currentUser
     * @return Response
     */
    public function viewAny(User $currentUser): Response
    {
        // If the current user is not an authenticated user with view permissions
        return $currentUser->hasRole(['Supervisor']) || $currentUser->id === 1
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de asignaturas.');
    }
}
