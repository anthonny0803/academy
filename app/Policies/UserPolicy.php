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
            ?? Response::allow();
    }
}
