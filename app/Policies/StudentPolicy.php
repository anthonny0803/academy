<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
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

    private function cannotPerformSupervisorActions(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para realizar esta acción.');
        }

        return null;
    }

    private function cannotWithdrawInactiveStudent(Student $student): ?Response
    {
        if (!$student->isActive()) {
            return Response::deny('El estudiante ya está inactivo.');
        }

        return null;
    }

    private function cannotWithdrawStudentWithoutActiveEnrollments(Student $student): ?Response
    {
        $hasActiveEnrollments = $student->enrollments()
            ->where('status', 'activo')
            ->exists();

        if (!$hasActiveEnrollments) {
            return Response::deny('El estudiante no tiene inscripciones activas para retirar.');
        }

        return null;
    }

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

    public function reassignRepresentative(User $currentUser): Response
    {
        return $this->cannotPerformSupervisorActions($currentUser)
            ?? Response::allow();
    }

    public function changeSituation(User $currentUser): Response
    {
        return $this->cannotManageStudents($currentUser)
            ?? Response::allow();
    }

    public function withdraw(User $currentUser, Student $student): Response
    {
        return $this->cannotPerformSupervisorActions($currentUser)
            ?? $this->cannotWithdrawInactiveStudent($student)
            ?? $this->cannotWithdrawStudentWithoutActiveEnrollments($student)
            ?? Response::allow();
    }
}
