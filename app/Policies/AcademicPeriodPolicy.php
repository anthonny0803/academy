<?php

namespace App\Policies;

use App\Models\AcademicPeriod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AcademicPeriodPolicy
{
    // Helper Methods - Permisos de usuario

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

    // Helper Methods - Reglas de eliminación

    private function cannotDeleteClosedPeriod(AcademicPeriod $academicPeriod): ?Response
    {
        if (!$academicPeriod->isActive()) {
            return Response::deny('No puedes eliminar un período académico cerrado. Contiene datos históricos importantes.');
        }

        return null;
    }

    private function cannotDeletePeriodWithActiveSections(AcademicPeriod $academicPeriod): ?Response
    {
        if ($academicPeriod->hasActiveSections()) {
            return Response::deny('No puedes eliminar un período académico con secciones activas. Desactívalas primero.');
        }

        return null;
    }

    // Helper Methods - Reglas de edición

    private function cannotUpdateClosedPeriod(AcademicPeriod $academicPeriod): ?Response
    {
        if (!$academicPeriod->isActive()) {
            return Response::deny('No puedes modificar un período académico cerrado.');
        }

        return null;
    }

    // Helper Methods - Reglas de cierre

    private function cannotCloseInactivePeriod(AcademicPeriod $academicPeriod): ?Response
    {
        if (!$academicPeriod->isActive()) {
            return Response::deny('No puedes cerrar un período académico que ya está inactivo.');
        }

        return null;
    }

    private function cannotClosePeriodWithoutSections(AcademicPeriod $academicPeriod): ?Response
    {
        if (!$academicPeriod->hasSections()) {
            return Response::deny('No puedes cerrar un período académico sin secciones. Considera eliminarlo en su lugar.');
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

    public function update(User $currentUser, AcademicPeriod $academicPeriod): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? $this->cannotUpdateClosedPeriod($academicPeriod)
            ?? Response::allow();
    }

    /**
     * Eliminar período académico
     * - No se puede eliminar si está cerrado (datos históricos)
     * - No se puede eliminar si tiene secciones activas
     * - Se puede eliminar si no tiene secciones o solo tiene secciones inactivas (cascada)
     */
    public function delete(User $currentUser, AcademicPeriod $academicPeriod): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? $this->cannotDeleteClosedPeriod($academicPeriod)
            ?? $this->cannotDeletePeriodWithActiveSections($academicPeriod)
            ?? Response::allow();
    }

    /**
     * Cerrar período académico (acción masiva)
     * Solo Developer y Supervisor pueden cerrar períodos
     * Requiere que el período tenga secciones
     */
    public function close(User $currentUser, AcademicPeriod $academicPeriod): Response
    {
        return $this->cannotManageAcademicPeriods($currentUser)
            ?? $this->cannotCloseInactivePeriod($academicPeriod)
            ?? $this->cannotClosePeriodWithoutSections($academicPeriod)
            ?? Response::allow();
    }
}