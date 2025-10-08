<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    private function cannotManageTeachers(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar profesores.');
        }

        return null;
    }

    private function cannotToggleTeachers(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para cambiar el estado de profesores.');
        }

        return null;
    }

    private function cannotModifyTeacherWithAdministrativeRole(User $currentUser, Teacher $teacher): ?Response
    {
        if ($teacher->user->isSupervisor() || $teacher->user->isAdmin() && !$currentUser->isDeveloper()) {
            if (!$currentUser->isDeveloper()) {
                return Response::deny('No se pueden modificar profesores con roles administrativos.');
            }
        }

        return null;
    }

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotManageTeachers($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, Teacher $teacher): Response
    {
        return $this->cannotManageTeachers($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageTeachers($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser, Teacher $teacher): Response
    {
        return $this->cannotManageTeachers($currentUser)
            ?? $this->cannotModifyTeacherWithAdministrativeRole($currentUser, $teacher)
            ?? Response::allow();
    }

    public function toggle(User $currentUser, Teacher $teacher): Response
    {
        return $this->cannotToggleTeachers($currentUser)
            ?? $this->cannotModifyTeacherWithAdministrativeRole($currentUser, $teacher)
            ?? Response::allow();
    }
}
