<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    private function isAuthorized(User $user): bool
    {
        return $user->isActive() && ($user->isDeveloper() || $user->isSupervisor());
    }

    private function cannotModifySelf(User $currentUser, User $targetUser): ?Response
    {
        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes realizar esta acción en tu propio usuario.');
        }

        return null;
    }

    private function cannotManageSameRoleSupervisor(User $currentUser, User $targetUser): ?Response
    {
        if ($currentUser->isDeveloper()) {
            return null;
        }

        if ($currentUser->isSupervisor() && $targetUser->isSupervisor()) {
            return Response::deny('No tienes autorización para gestionar usuarios con tu mismo rol.');
        }

        return null;
    }

    public function viewAny(User $currentUser): Response
    {
        return $this->isAuthorized($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para ver el módulo de usuarios.');
    }

    public function view(User $currentUser, User $targetUser): Response
    {
        if (!$this->isAuthorized($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper() && !$currentUser->isDeveloper()) {
            return Response::deny('No tienes autorización para ver este usuario.');
        }

        return Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->isAuthorized($currentUser)
            ? Response::allow()
            : Response::deny('No tienes autorización para crear usuarios.');
    }

    public function edit(User $currentUser, User $targetUser): Response
    {
        if (!$this->isAuthorized($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper()) {
            return Response::deny('No se puede modificar este usuario.');
        }

        return $this->cannotModifySelf($currentUser, $targetUser)
            ?? $this->cannotManageSameRoleSupervisor($currentUser, $targetUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, User $targetUser): Response
    {
        if (!$this->isAuthorized($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper()) {
            return Response::deny('No puedes eliminar este usuario.');
        }

        if ($targetUser->isActive()) {
            return Response::deny('No puedes eliminar un usuario activo.');
        }

        if ($targetUser->isRepresentative() && $targetUser->representative?->hasStudents()) {
            return Response::deny('No puedes eliminar a un usuario que tiene estudiantes.');
        }

        return $this->cannotModifySelf($currentUser, $targetUser)
            ?? $this->cannotManageSameRoleSupervisor($currentUser, $targetUser)
            ?? Response::allow();
    }

    public function toggle(User $currentUser, User $targetUser): Response
    {
        if (!$this->isAuthorized($currentUser)) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        if ($targetUser->isDeveloper()) {
            return Response::deny('No se puede cambiar el estado de usuarios desarrolladores.');
        }

        return $this->cannotModifySelf($currentUser, $targetUser)
            ?? $this->cannotManageSameRoleSupervisor($currentUser, $targetUser)
            ?? Response::allow();
    }
}
