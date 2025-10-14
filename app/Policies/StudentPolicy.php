<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    // Helper Methods

    private function cannotViewStudents(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver estudiantes.');
        }

        return null;
    }

    private function cannotManageStudents(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar estudiantes.');
        }

        return null;
    }

    private function cannotToggleStudent(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para cambiar el estado de estudiantes.');
        }

        return null;
    }

    private function cannotReassignRepresentative(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para reasignar representantes.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewStudents($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser): Response
    {
        return $this->cannotViewStudents($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageStudents($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser): Response
    {
        return $this->cannotManageStudents($currentUser)
            ?? Response::allow();
    }

    public function toggle(User $currentUser): Response
    {
        return $this->cannotToggleStudent($currentUser)
            ?? Response::allow();
    }

    public function reassignRepresentative(User $currentUser): Response
    {
        return $this->cannotReassignRepresentative($currentUser)
            ?? Response::allow();
    }
}
