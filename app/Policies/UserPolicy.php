<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    // Helper Methods

    private function cannotManageUsers(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para gestionar usuarios.');
        }

        return null;
    }

    private function cannotManageRoles(User $currentUser): ?Response
    {
        if (!$currentUser->isActive() || (!$currentUser->isDeveloper() && !$currentUser->isSupervisor() && !$currentUser->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar roles.');
        }

        return null;
    }

    private function cannotManageDeveloper(User $targetUser): ?Response
    {
        if ($targetUser->isDeveloper()) {
            return Response::deny('No se puede gestionar este usuario.');
        }

        return null;
    }

    private function cannotModifySelf(User $currentUser, User $targetUser): ?Response
    {
        if ($currentUser->id === $targetUser->id) {
            return Response::deny('No puedes gestionar tu propio usuario.');
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

    private function cannotToggleWithoutAdministrativeRole(User $targetUser): ?Response
    {
        if (!$targetUser->isSupervisor() && !$targetUser->isAdmin()) {
            return Response::deny('No puedes activar un usuario si no tiene rol administrativo.');
        }

        return null;
    }

    private function cannotDeleteUserWithConditions(User $targetUser): ?Response
    {
        if ($targetUser->isActive()) {
            return Response::deny('No puedes eliminar un usuario activo.');
        }

        if ($targetUser->isRepresentative() && $targetUser->representative?->hasStudents()) {
            return Response::deny('No puedes eliminar a un usuario que tiene estudiantes.');
        }

        return null;
    }

    private function cannotAssignRolesToUser(User $currentUser, User $targetUser): ?Response
    {
        if ($targetUser->isDeveloper()) {
            return Response::deny('No se puede asignar roles a este usuario.');
        }

        if (!$currentUser->isActive() || (!$currentUser->isDeveloper() && !$currentUser->isSupervisor() && !$currentUser->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar roles.');
        }

        if (!$currentUser->isDeveloper() && $currentUser->isSupervisor()) {
            if ($targetUser->isSupervisor()) {
                return Response::deny('No tienes autorización para cambiar roles administrativos de usuarios con tu mismo rol.');
            }
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotManageUsers($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, User $targetUser): Response
    {
        return $this->cannotManageUsers($currentUser)
            ?? $this->cannotManageDeveloper($targetUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageUsers($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser, User $targetUser): Response
    {
        return $this->cannotManageUsers($currentUser)
            ?? $this->cannotManageDeveloper($targetUser)
            ?? $this->cannotModifySelf($currentUser, $targetUser)
            ?? $this->cannotManageSameRoleSupervisor($currentUser, $targetUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, User $targetUser): Response
    {
        return $this->cannotManageUsers($currentUser)
            ?? $this->cannotManageDeveloper($targetUser)
            ?? $this->cannotModifySelf($currentUser, $targetUser)
            ?? $this->cannotManageSameRoleSupervisor($currentUser, $targetUser)
            ?? $this->cannotDeleteUserWithConditions($targetUser)
            ?? Response::allow();
    }

    public function toggle(User $currentUser, User $targetUser): Response
    {
        return $this->cannotManageUsers($currentUser)
            ?? $this->cannotManageDeveloper($targetUser)
            ?? $this->cannotModifySelf($currentUser, $targetUser)
            ?? $this->cannotManageSameRoleSupervisor($currentUser, $targetUser)
            ?? $this->cannotToggleWithoutAdministrativeRole($targetUser)
            ?? Response::allow();
    }

    public function assignView(User $currentUser): Response
    {
        return $this->cannotManageRoles($currentUser)
            ?? Response::allow();
    }

    public function assign(User $currentUser, User $targetUser): Response
    {
        return $this->cannotAssignRolesToUser($currentUser, $targetUser)
            ?? Response::allow();
    }
}
