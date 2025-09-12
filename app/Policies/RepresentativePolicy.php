<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Representative;
use Illuminate\Auth\Access\Response;

class RepresentativePolicy
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
        return $currentUser->hasRole(['SuperAdmin', 'Administrador']) || $currentUser->id === 1
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de representantes.');
    }

    /**
     * Determine whether the user can view a specific representative.
     */
    public function view(User $currentUser, Representative $representative): Response
    {
        // The current user can view all representatives except the developer user
        return ($currentUser->hasRole(['SuperAdmin', 'Administrador']) && $representative->user_id !== 1)
            || $currentUser->id === 1
            ? Response::allow()
            : Response::deny('No tienes autorización para ver este representante.');
    }

    /**
     * Determine whether the user can create representatives.
     * @param User $currentUser
     * @return Response
     */
    public function create(User $currentUser): Response
    {
        return $currentUser->hasRole(['SuperAdmin', 'Administrador'])
            ? Response::allow()
            : Response::deny('No tienes autorización para crear representantes.');
    }

    /**
     * Determine whether the user can update the representative.
     * @param User $currentUser
     * @param Representative $representative
     * @return Response
     */
    public function edit(User $currentUser, Representative $representative): Response
    {
        return $currentUser->hasRole(['SuperAdmin', 'Administrador'])
            ? Response::allow()
            : Response::deny('No tienes autorización para editar este representante.');
    }

    /**
     * Determine whether the user can delete the representative.
     * @param User $currentUser
     * @param Representative $representative
     * @return Response
     */
    public function delete(User $currentUser, Representative $representative): Response
    {
        return $currentUser->hasRole(['SuperAdmin'])
            ? Response::allow()
            : Response::deny('No tienes autorización para eliminar representantes.');
    }
}
