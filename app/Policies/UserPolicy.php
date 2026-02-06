<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Role;
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

        if ($targetUser->hasEntity()) {
            return Response::deny('No puedes eliminar un usuario que tiene perfiles asociados.');
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
            if ($targetUser->isSupervisor() && $currentUser->id !== $targetUser->id) {
                return Response::deny('No tienes autorización para cambiar roles administrativos de usuarios con tu mismo rol.');
            }
        }

        return null;
    }

    private function cannotSelfDemote(User $currentUser, User $targetUser, Role $role): ?Response
    {
        if ($currentUser->id === $targetUser->id && $currentUser->isSupervisor() && $role === Role::Admin) {
            return Response::deny('No puedes degradar tu propio rol administrativo.');
        }

        return null;
    }

    private function cannotAdminManageHigherOrEqualRole(User $currentUser, User $targetUser): ?Response
    {
        if (!$currentUser->isDeveloper() && !$currentUser->isSupervisor() && $currentUser->isAdmin()) {
            if ($targetUser->isSupervisor() || $targetUser->isAdmin()) {
                return Response::deny('No tienes autorización para gestionar roles de este usuario.');
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

    public function assignManage(User $currentUser, User $targetUser): Response
    {
        return $this->cannotManageRoles($currentUser)
            ?? $this->cannotManageDeveloper($targetUser)
            ?? $this->cannotAdminManageHigherOrEqualRole($currentUser, $targetUser)
            ?? Response::allow();
    }

    public function assign(User $currentUser, User $targetUser, ?Role $role = null): Response
    {
        return $this->cannotAssignRolesToUser($currentUser, $targetUser)
            ?? ($role ? $this->cannotSelfDemote($currentUser, $targetUser, $role) : null)
            ?? Response::allow();
    }
}
