<?php

namespace App\Policies;

use App\Models\GradeColumn;
use App\Models\SectionSubjectTeacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GradeColumnPolicy
{
    // =========================================
    // Helper: Verificar acceso según rol
    // =========================================

    private function isActiveForGradeColumns(User $user): bool
    {
        // Developer, Supervisor, Admin → verificar user.is_active
        if ($user->isDeveloper() || $user->isSupervisor() || $user->isAdmin()) {
            return $user->isActive();
        }

        // Teacher → verificar teacher.is_active (no user.is_active)
        if ($user->isTeacher() && $user->teacher) {
            return $user->teacher->isActive();
        }

        return false;
    }

    // =========================================
    // Helper Methods
    // =========================================

    private function cannotViewGradeColumns(User $user): ?Response
    {
        if (!$this->isActiveForGradeColumns($user)) {
            if ($user->isTeacher()) {
                return Response::deny('Tu perfil de profesor no está activo.');
            }
            return Response::deny('Tu usuario no está activo.');
        }

        if (!$user->isDeveloper() 
            && !$user->isSupervisor() 
            && !$user->isAdmin() 
            && !$user->isTeacher()
        ) {
            return Response::deny('No tienes autorización para ver configuraciones de evaluación.');
        }

        return null;
    }

    private function cannotManageGradeColumns(User $user): ?Response
    {
        // Developer siempre puede (si user activo)
        if ($user->isDeveloper()) {
            if (!$user->isActive()) {
                return Response::deny('Tu usuario no está activo.');
            }
            return null;
        }

        // Teacher necesita teacher.is_active
        if ($user->isTeacher()) {
            if (!$user->teacher || !$user->teacher->isActive()) {
                return Response::deny('Tu perfil de profesor no está activo.');
            }
            return null;
        }

        return Response::deny('Solo los profesores pueden gestionar configuraciones de evaluación.');
    }

    private function cannotManageThisAssignment(User $user, SectionSubjectTeacher $sst): ?Response
    {
        if ($user->isDeveloper()) {
            return null;
        }

        if (!$user->teacher || $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No eres el profesor asignado a esta materia/sección.');
        }

        if ($sst->status !== 'activo') {
            return Response::deny('Esta asignación no está activa.');
        }

        return null;
    }

    private function cannotModifyColumnWithGrades(GradeColumn $gradeColumn): ?Response
    {
        if ($gradeColumn->hasGrades()) {
            return Response::deny('No puedes modificar una evaluación que ya tiene notas registradas.');
        }

        return null;
    }

    private function cannotDeleteColumnWithGrades(GradeColumn $gradeColumn): ?Response
    {
        if ($gradeColumn->hasGrades()) {
            return Response::deny('No puedes eliminar una evaluación que tiene notas registradas.');
        }

        return null;
    }

    // =========================================
    // Policy Methods
    // =========================================

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewGradeColumns($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, GradeColumn $gradeColumn): Response
    {
        $denyView = $this->cannotViewGradeColumns($currentUser);
        if ($denyView) return $denyView;

        // Developer, Supervisor, Admin pueden ver cualquier columna
        if ($currentUser->isDeveloper() || $currentUser->isSupervisor() || $currentUser->isAdmin()) {
            return Response::allow();
        }

        // Teacher solo puede ver columnas de sus asignaciones
        if ($currentUser->isTeacher() && $currentUser->teacher) {
            if ($gradeColumn->sectionSubjectTeacher->teacher_id !== $currentUser->teacher->id) {
                return Response::deny('Esta configuración no corresponde a tu asignación.');
            }
        }

        return Response::allow();
    }

    public function create(User $currentUser, SectionSubjectTeacher $sst): Response
    {
        return $this->cannotManageGradeColumns($currentUser)
            ?? $this->cannotManageThisAssignment($currentUser, $sst)
            ?? Response::allow();
    }

    public function update(User $currentUser, GradeColumn $gradeColumn): Response
    {
        return $this->cannotManageGradeColumns($currentUser)
            ?? $this->cannotManageThisAssignment($currentUser, $gradeColumn->sectionSubjectTeacher)
            ?? $this->cannotModifyColumnWithGrades($gradeColumn)
            ?? Response::allow();
    }

    public function delete(User $currentUser, GradeColumn $gradeColumn): Response
    {
        return $this->cannotManageGradeColumns($currentUser)
            ?? $this->cannotManageThisAssignment($currentUser, $gradeColumn->sectionSubjectTeacher)
            ?? $this->cannotDeleteColumnWithGrades($gradeColumn)
            ?? Response::allow();
    }
}