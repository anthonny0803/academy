<?php

namespace App\Domains\Academics\Policies;

use App\Domains\Identity\Models\User;
use Illuminate\Auth\Access\Response;

class SubjectTeacherPolicy
{
    // Helper Methods

    private function cannotManageSubjectTeacher(User $user): ?Response
    {
        if (! $user->isActive() || (! $user->isDeveloper() && ! $user->isSupervisor() && ! $user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar materias de profesores.');
        }

        return null;
    }

    private function cannotViewSubjectTeacher(User $user): ?Response
    {
        if (! $user->isActive() || (! $user->isDeveloper() && ! $user->isSupervisor() && ! $user->isAdmin())) {
            return Response::deny('No tienes autorización para ver materias de profesores.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewSubjectTeacher($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageSubjectTeacher($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser): Response
    {
        return $this->cannotManageSubjectTeacher($currentUser)
            ?? Response::allow();
    }
}
