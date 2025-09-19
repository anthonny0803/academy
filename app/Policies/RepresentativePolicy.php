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
        return $currentUser->hasRole(['Supervisor', 'Administrador']) || $currentUser->id === 1
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de representantes.');
    }

    /**
     * Determine whether the user can view a specific representative.
     */
    public function view(User $currentUser): Response
    {
        // Only Supervisor and Administrador roles can view representatives.
        return $currentUser->hasRole(['Supervisor', 'Administrador'])
            ? Response::allow()
            : Response::deny('No tienes autorización para ver representantes.');
    }

    /**
     * Determine whether the user can create representatives.
     * @param User $currentUser
     * @return Response
     */
    public function create(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor'])
            ? Response::allow()
            : Response::deny('No tienes autorización para crear representantes.');
    }

    /**
     * Determine whether the user can edit the representative.
     * @param User $currentUser
     * @param Representative $representative
     * @return Response
     */
    public function edit(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor', 'Administrador'])
            ? Response::allow()
            : Response::deny('No tienes autorización para editar este representante.');
    }

    /**
     * Determine whether the user can update the representative.
     * @param User $currentUser
     * @param Representative $representative
     * @return Response
     */
    public function update(User $currentUser, Representative $representative): Response
    {
        // Check role permission first.
        if (!$currentUser->hasRole(['Supervisor', 'Administrador'])) {
            return Response::deny('No tienes autorización para editar representantes.');
        }

        // Block sensitive data from de employee.
        if ($representative->user->hasRole(['Supervisor', 'Administrador', 'Profesor'])) {
            return Response::deny('No se pueden modificar campos sensibles de empleados.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the representative.
     * @param User $currentUser
     * @param Representative $representative
     * @return Response
     */
    public function delete(User $currentUser, Representative $representative): Response
    {
        return $currentUser->hasRole(['Supervisor'])
            ? Response::allow()
            : Response::deny('No tienes autorización para eliminar representantes.');
    }
}
