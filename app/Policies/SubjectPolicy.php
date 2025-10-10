<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubjectPolicy
{
    // Helper Methods

    private function cannotViewSubjects(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver asignaturas.');
        }

        return null;
    }

    private function cannotManageSubjects(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para gestionar asignaturas.');
        }

        return null;
    }

    private function cannotDeleteActiveSubject(Subject $subject): ?Response
    {
        if ($subject->isActive()) {
            return Response::deny('No puedes eliminar una asignatura activa. Desactívala primero.');
        }

        return null;
    }

    private function cannotDeleteSubjectWithGrades(Subject $subject): ?Response
    {
        if ($subject->grades()->exists()) {
            return Response::deny('No puedes eliminar una asignatura con calificaciones registradas.');
        }

        return null;
    }

    private function cannotDeleteSubjectWithAssignments(Subject $subject): ?Response
    {
        if ($subject->sectionSubjectTeachers()->exists()) {
            return Response::deny('No puedes eliminar una asignatura con asignaciones a secciones.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewSubjects($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageSubjects($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser, Subject $subject): Response
    {
        return $this->cannotManageSubjects($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, Subject $subject): Response
    {
        return $this->cannotManageSubjects($currentUser)
            ?? $this->cannotDeleteActiveSubject($subject)
            ?? $this->cannotDeleteSubjectWithGrades($subject)
            ?? $this->cannotDeleteSubjectWithAssignments($subject)
            ?? Response::allow();
    }

    public function toggle(User $currentUser, Subject $subject): Response
    {
        return $this->cannotManageSubjects($currentUser)
            ?? Response::allow();
    }
}
