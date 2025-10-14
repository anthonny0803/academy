<?php

namespace App\Policies;

use App\Models\AcademicPeriod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AcademicPeriodPolicy
{
    // Helper Methods

    private function cannotViewAcademicPeriods(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver períodos académicos.');
        }

        return null;
    }

    private function cannotManageAcademicPeriods(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor())) {
            return Response::deny('No tienes autorización para gestionar períodos académicos.');
        }

        return null;
    }

    private function cannotDeleteActiveAcademicPeriod(AcademicPeriod $academicPeriod): ?Response
    {
        if ($academicPeriod->isActive()) {
            return Response::deny('No puedes eliminar un período académico activo. Desactívalo primero.');
        }

        return null;
    }

    private function cannotDeleteAcademicPeriodWithSections(AcademicPeriod $academicPeriod): ?Response
    {
        if ($academicPeriod->sections()->exists()) {
            return Response::deny('No puedes eliminar un período académico con secciones asociadas.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewAcademicPeriods($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser): Response
    {
        return $this->cannotViewAcademicPeriods($currentUser)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? Response::allow();
    }

    public function update(User $currentUser): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? Response::allow();
    }

    public function delete(User $currentUser, AcademicPeriod $academicPeriod): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? $this->cannotDeleteActiveAcademicPeriod($academicPeriod)
            ?? $this->cannotDeleteAcademicPeriodWithSections($academicPeriod)
            ?? Response::allow();
    }

    public function toggle(User $currentUser): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? Response::allow();
    }
}
