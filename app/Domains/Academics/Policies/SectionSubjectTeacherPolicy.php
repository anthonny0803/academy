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

    // Policy Methods

    public function create(User $currentUser): Response
    {
        return $this->cannotManageAssignments($currentUser)
            ?? Response::allow();
    }

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
