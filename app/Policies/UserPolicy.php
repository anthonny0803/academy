<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    private function isActiveWithHighRole(User $user): bool
    {
        return $user->isActive() && ($user->isSupervisor() || $user->isDeveloper());
    }

    public function viewAny(User $currentUser): Response
    {
        return $this->isActiveWithHighRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de usuarios.');
    }

    public function view(User $currentUser, User $targetUser): Response
    {
        if (! $this->isActiveWithHighRole($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper() && ! $currentUser->isDeveloper()) {
            return Response::deny('No tienes autorización para ver este usuario.');
        }

        return Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->isActiveWithHighRole($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para crear usuarios.');
    }

    public function edit(User $currentUser, User $targetUser): Response
    {
        if (! $this->isActiveWithHighRole($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper()) {
            return Response::deny('No se puede modificar este usuario.');
        }

        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes modificar tu usuario.');
        }

        if ($currentUser->isSupervisor() && ! $currentUser->isDeveloper() && $targetUser->isSupervisor()) {
            return Response::deny('No tienes autorización para modificar usuarios con tu rol.');
        }

        return Response::allow();
    }

    public function delete(User $currentUser, User $targetUser): Response
    {
        if (! $this->isActiveWithHighRole($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper()) {
            return Response::deny('No puedes eliminar este usuario.');
        }

        if ($currentUser->isSupervisor() && ! $currentUser->isDeveloper() && $targetUser->isSupervisor()) {
            return Response::deny('No puedes eliminar usuarios con tu rol.');
        }

        if ($targetUser->is_active) {
            return Response::deny('No puedes eliminar un usuario activo.');
        }

        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes eliminar tu usuario.');
        }

        if ($targetUser->isRepresentative() && $targetUser->hasStudents()) {
            return Response::deny('No puedes eliminar a un usuario que tiene estudiantes.');
        }

        return Response::allow();
    }

    public function toggle(User $currentUser, User $targetUser): Response
    {
        if (! $this->isActiveWithHighRole($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper()) {
            return Response::deny('No se puede cambiar el estado de este usuario.');
        }

        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No tienes autorización para cambiar el estado de tu usuario.');
        }

        if ($currentUser->isSupervisor() && ! $currentUser->isDeveloper() && $targetUser->isSupervisor()) {
            return Response::deny('No tienes autorización para cambiar el estado de este usuario.');
        }

        return Response::allow();
    }
}
