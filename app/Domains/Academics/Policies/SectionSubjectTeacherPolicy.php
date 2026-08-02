<?php

namespace App\Domains\Academics\Policies;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Identity\Models\User;
use Illuminate\Auth\Access\Response;

class SectionSubjectTeacherPolicy
{
    // Helper Methods

    private function cannotManageAssignments(User $user): ?Response
    {
        if (! $user->isActive() || (! $user->isDeveloper() && ! $user->isSupervisor() && ! $user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar asignaciones de profesores.');
        }

        return null;
    }

    private function cannotViewAssignments(User $user): ?Response
    {
        if (! $user->isActive()) {
            return Response::deny('Tu usuario no está activo.');
        }

        if (! $user->isDeveloper()
            && ! $user->isSupervisor()
            && ! $user->isAdmin()
            && ! $user->isTeacher()
        ) {
            return Response::deny('No tienes autorización para ver asignaciones de profesores.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewAssignments($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageAssignments($currentUser)
            ?? Response::allow();
    }

    /**
     * The assignment is unused today — managing one is a role-wide ability — but
     * the parameter is what makes this an object-level check, so a caller cannot
     * authorize the class while holding the bound instance.
     */
    public function update(User $currentUser, SectionSubjectTeacher $sst): Response
    {
        return $this->cannotManageAssignments($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, SectionSubjectTeacher $sst): Response
    {
        if (! $currentUser->isActive() || ! $currentUser->isDeveloper()) {
            return Response::deny('Solo los desarrolladores pueden eliminar asignaciones.');
        }

        return Response::allow();
    }
}
