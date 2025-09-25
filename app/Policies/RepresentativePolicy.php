<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Representative;
use Illuminate\Auth\Access\Response;

class RepresentativePolicy
{
    public function viewAny(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor', 'Administrador']) || $currentUser->id === 1
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de representantes.');
    }

    public function view(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor', 'Administrador'])
            ? Response::allow()
            : Response::deny('No tienes autorización para ver representantes.');
    }

    public function create(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor'])
            ? Response::allow()
            : Response::deny('No tienes autorización para crear representantes.');
    }

    public function edit(User $currentUser): Response
    {
        return $currentUser->hasRole(['Supervisor', 'Administrador'])
            ? Response::allow()
            : Response::deny('No tienes autorización para editar este representante.');
    }

    public function update(User $currentUser, Representative $representative): Response
    {
        if (!$currentUser->hasRole(['Supervisor', 'Administrador'])) {
            return Response::deny('No tienes autorización para editar representantes.');
        }

        if ($representative->user->hasRole(['Supervisor', 'Administrador', 'Profesor'])) {
            return Response::deny('No se pueden modificar campos sensibles de empleados.');
        }

        return Response::allow();
    }

    public function delete(User $currentUser, Representative $representative): Response
    {
        return $currentUser->hasRole(['Supervisor'])
            ? Response::allow()
            : Response::deny('No tienes autorización para eliminar representantes.');
    }

    public function toggle(User $currentUser, Representative $representative): Response
    {
        if (!$currentUser->hasRole('Supervisor')) {
            return Response::deny(
                'No tienes autorización para cambiar el estado de los representantes.'
            );
        }

        return Response::allow();
    }
}
