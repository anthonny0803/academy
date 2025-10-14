<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    // Helper Methods

    private function cannotViewEnrollments(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver inscripciones.');
        }

        return null;
    }

    private function cannotManageEnrollments(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para gestionar inscripciones.');
        }

        return null;
    }

    private function cannotModifyEnrollments(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para modificar inscripciones.');
        }

        return null;
    }

    private function cannotDeleteActiveEnrollment(Enrollment $enrollment): ?Response
    {
        if (!$enrollment->isActive()) {
            return Response::deny('Solo se pueden eliminar inscripciones activas. Usa "Retirar" para cambiar el estado.');
        }

        return null;
    }

    private function cannotDeleteEnrollmentWithGrades(Enrollment $enrollment): ?Response
    {
        if ($enrollment->grades()->exists()) {
            return Response::deny('No puedes eliminar una inscripción con calificaciones registradas.');
        }

        return null;
    }

    private function cannotTransferNonActiveEnrollment(Enrollment $enrollment): ?Response
    {
        if (!$enrollment->isActive()) {
            return Response::deny('Solo se pueden transferir inscripciones activas.');
        }

        return null;
    }

    private function cannotPromoteNonActiveEnrollment(Enrollment $enrollment): ?Response
    {
        if (!$enrollment->isActive()) {
            return Response::deny('Solo se pueden promover inscripciones activas.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewEnrollments($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser): Response
    {
        return $this->cannotViewEnrollments($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageEnrollments($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser): Response
    {
        return $this->cannotModifyEnrollments($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, Enrollment $enrollment): Response
    {
        return $this->cannotModifyEnrollments($currentUser)
            ?? $this->cannotDeleteActiveEnrollment($enrollment)
            ?? $this->cannotDeleteEnrollmentWithGrades($enrollment)
            ?? Response::allow();
    }

    public function transfer(User $currentUser, Enrollment $enrollment): Response
    {
        return $this->cannotModifyEnrollments($currentUser)
            ?? $this->cannotTransferNonActiveEnrollment($enrollment)
            ?? Response::allow();
    }

    public function promote(User $currentUser, Enrollment $enrollment): Response
    {
        return $this->cannotModifyEnrollments($currentUser)
            ?? $this->cannotPromoteNonActiveEnrollment($enrollment)
            ?? Response::allow();
    }
}
