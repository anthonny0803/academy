<?php

namespace App\Policies;

use App\Models\SectionSubjectTeacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SectionSubjectTeacherPolicy
{
    // Helper Methods

    private function cannotManageAssignments(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar asignaciones de profesores.');
        }

        return null;
    }

    private function cannotViewAssignments(User $user): ?Response
    {
        if (!$user->isActive()) {
            return Response::deny('Tu usuario no está activo.');
        }

        if (!$user->isDeveloper() 
            && !$user->isSupervisor() 
            && !$user->isAdmin() 
            && !$user->isTeacher()
        ) {
            return Response::deny('No tienes autorización para ver asignaciones de profesores.');
        }

        return null;
    }

    private function cannotViewThisAssignment(User $user, SectionSubjectTeacher $sst): ?Response
    {
        // Developer, Supervisor, Admin pueden ver cualquier asignación
        if ($user->isDeveloper() || $user->isSupervisor() || $user->isAdmin()) {
            return null;
        }

        // Teacher puede ver solo sus propias asignaciones
        if ($user->isTeacher() && $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No tienes autorización para ver esta asignación.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewAssignments($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, SectionSubjectTeacher $sst): Response
    {
        return $this->cannotViewAssignments($currentUser)
            ?? $this->cannotViewThisAssignment($currentUser, $sst)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageAssignments($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser): Response
    {
        return $this->cannotManageAssignments($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser): Response
    {
        if (!$currentUser->isActive() || !$currentUser->isDeveloper()) {
            return Response::deny('Solo los desarrolladores pueden eliminar asignaciones.');
        }

        return Response::allow();
    }
}