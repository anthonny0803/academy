<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    private function isActiveWithAllRole(User $user): bool
    {
        return $user->isActive() && ($user->isSupervisor() || $user->isAdmin() || $user->isDeveloper());
    }

    private function isActiveWithHighRole(User $user): bool
    {
        return $user->isActive() && ($user->isSupervisor() || $user->isDeveloper());
    }

    public function viewAny(User $currentUser): Response
    {
        return $this->isActiveWithAllRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de profesores.');
    }

    public function view(User $currentUser): Response
    {
        return $this->isActiveWithAllRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para ver profesores.');
    }

    public function create(User $currentUser): Response
    {
        return $this->isActiveWithAllRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para crear profesores.');
    }

    public function edit(User $currentUser): Response
    {
        return $this->isActiveWithAllRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para editar este profesor.');
    }

    public function update(User $currentUser, Teacher $teacher): Response
    {
        if (! $this->isActiveWithAllRole($currentUser)) {
            return Response::deny('No tienes autorización para editar profesores.');
        }

        if ($teacher->user->isSupervisor() || $teacher->user->isAdmin() || $teacher->user->isTeacher()) {
            return Response::deny('No se pueden modificar campos sensibles de empleados.');
        }

        return Response::allow();
    }

    public function delete(User $currentUser): Response
    {
        return $this->isActiveWithHighRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para eliminar profesores.');
    }

    public function toggle(User $currentUser): Response
    {
        return $this->isActiveWithHighRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para cambiar el estado de los profesores.');
    }
}
