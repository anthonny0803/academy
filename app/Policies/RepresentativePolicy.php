<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Representative;
use Illuminate\Auth\Access\Response;

class RepresentativePolicy
{
    public function viewAny(User $currentUser): Response
    {
        return $currentUser->isSupervisor() || $currentUser->isAdmin() || $currentUser->isDeveloper()
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de representantes.');
    }

    public function view(User $currentUser): Response
    {
        return $currentUser->isSupervisor() || $currentUser->isAdmin() || $currentUser->isDeveloper()
            ? Response::allow()
            : Response::deny('No tienes autorización para ver representantes.');
    }

    public function create(User $currentUser): Response
    {
        return $currentUser->isSupervisor() || $currentUser->isAdmin() || $currentUser->isDeveloper()
            ? Response::allow()
            : Response::deny('No tienes autorización para crear representantes.');
    }

    public function edit(User $currentUser): Response
    {
        return $currentUser->isSupervisor() || $currentUser->isAdmin() || $currentUser->isDeveloper()
            ? Response::allow()
            : Response::deny('No tienes autorización para editar este representante.');
    }

    public function update(User $currentUser, Representative $representative): Response
    {
        if (!$currentUser->isSupervisor() && !$currentUser->isAdmin() && !$currentUser->isDeveloper()) {
            return Response::deny('No tienes autorización para editar representantes.');
        }

        if ($representative->user->isSupervisor() || $representative->user->isAdmin() || $representative->user->isTeacher()) {
            return Response::deny('No se pueden modificar campos sensibles de empleados.');
        }

        return Response::allow();
    }

    public function delete(User $currentUser): Response
    {
        return $currentUser->isSupervisor() || $currentUser->isDeveloper()
            ? Response::allow()
            : Response::deny('No tienes autorización para eliminar representantes.');
    }

    public function toggle(User $currentUser): Response
    {
        return $currentUser->isSupervisor() || $currentUser->isDeveloper()
            ? Response::allow()
            : Response::deny('No tienes autorización para cambiar el estado de los representantes.');
    }
}
