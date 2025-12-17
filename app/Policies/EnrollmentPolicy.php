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
            return Response::deny('Solo se pueden eliminar inscripciones activas.');
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

    private function cannotActOnNonActiveEnrollment(Enrollment $enrollment, string $action): ?Response
    {
        if (!$enrollment->isActive()) {
            return Response::deny("Solo se pueden {$action} inscripciones activas.");
        }

        return null;
    }

    private function cannotPromoteInNonPromotablePeriod(Enrollment $enrollment): ?Response
    {
        if (!$enrollment->section->academicPeriod->isPromotable()) {
            return Response::deny('Este período académico no permite promociones.');
        }

        return null;
    }

    private function cannotTransferInNonTransferablePeriod(Enrollment $enrollment): ?Response
    {
        if (!$enrollment->section->academicPeriod->isTransferable()) {
            return Response::deny('Este período académico no permite transferencias.');
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
            ?? $this->cannotActOnNonActiveEnrollment($enrollment, 'transferir')
            ?? $this->cannotTransferInNonTransferablePeriod($enrollment)
            ?? Response::allow();
    }

    public function promote(User $currentUser, Enrollment $enrollment): Response
    {
        return $this->cannotModifyEnrollments($currentUser)
            ?? $this->cannotActOnNonActiveEnrollment($enrollment, 'promover')
            ?? $this->cannotPromoteInNonPromotablePeriod($enrollment)
            ?? Response::allow();
    }
}