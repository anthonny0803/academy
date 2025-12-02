<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeColumn;
use App\Models\User;
use App\Models\SectionSubjectTeacher;
use Illuminate\Auth\Access\Response;

class GradePolicy
{
    // =========================================
    // Helper: Verificar acceso según rol
    // =========================================

    private function isActiveForGrades(User $user): bool
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

    private function cannotViewGrades(User $user): ?Response
    {
        if (!$this->isActiveForGrades($user)) {
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
            return Response::deny('No tienes autorización para ver calificaciones.');
        }

        return null;
    }

    private function cannotViewThisGrade(User $user, Grade $grade): ?Response
    {
        // Developer, Supervisor, Admin pueden ver cualquier calificación
        if ($user->isDeveloper() || $user->isSupervisor() || $user->isAdmin()) {
            return null;
        }

        // Teacher solo sus asignaciones
        if ($user->isTeacher() && $user->teacher) {
            $sst = $grade->gradeColumn->sectionSubjectTeacher;
            if ($sst->teacher_id !== $user->teacher->id) {
                return Response::deny('Esta calificación no corresponde a tu asignación.');
            }
            return null;
        }

        // Representative puede ver notas de sus estudiantes
        if ($user->isRepresentative() && $user->representative) {
            $isHisStudent = $user->representative->students()
                ->whereHas('enrollments', fn($q) => $q->where('id', $grade->enrollment_id))
                ->exists();

            $isOwnGrade = $user->isStudent() 
                && $user->student 
                && $grade->enrollment->student_id === $user->student->id;

            if (!$isHisStudent && !$isOwnGrade) {
                return Response::deny('Esta calificación no te corresponde a ti ni a tus estudiantes.');
            }
            return null;
        }

        // Student solo sus propias notas
        if ($user->isStudent() && $user->student) {
            if ($grade->enrollment->student_id !== $user->student->id) {
                return Response::deny('Esta calificación no es tuya.');
            }
            return null;
        }

        return Response::deny('No tienes autorización para ver esta calificación.');
    }

    private function cannotManageGrades(User $user): ?Response
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

        return Response::deny('Solo los profesores pueden gestionar calificaciones.');
    }

    private function cannotManageThisGrade(User $user, Grade $grade): ?Response
    {
        if ($user->isDeveloper()) {
            return null;
        }

        $sst = $grade->gradeColumn->sectionSubjectTeacher;

        if (!$user->teacher || $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No eres el profesor asignado a esta materia/sección.');
        }

        if ($sst->status !== 'activo') {
            return Response::deny('Esta asignación no está activa.');
        }

        return null;
    }

    private function cannotCreateGradeForColumn(User $user, GradeColumn $gradeColumn, Enrollment $enrollment): ?Response
    {
        if ($user->isDeveloper()) {
            return null;
        }

        $sst = $gradeColumn->sectionSubjectTeacher;

        // Verificar que es su asignación
        if (!$user->teacher || $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No puedes calificar en esta asignatura/sección.');
        }

        // Verificar asignación activa
        if ($sst->status !== 'activo') {
            return Response::deny('Esta asignación no está activa.');
        }

        // Verificar que el estudiante pertenece a esta sección
        if ($enrollment->section_id !== $sst->section_id) {
            return Response::deny('El estudiante no pertenece a esta sección.');
        }

        // Verificar inscripción activa
        if ($enrollment->status !== 'activo') {
            return Response::deny('La inscripción del estudiante no está activa.');
        }

        // Verificar que la configuración está completa (suma 100%)
        if (!$sst->isConfigurationComplete()) {
            return Response::deny('La configuración de evaluaciones debe sumar 100% antes de calificar.');
        }

        return null;
    }

    // =========================================
    // Policy Methods
    // =========================================

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewGrades($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, Grade $grade): Response
    {
        $denyView = $this->cannotViewGrades($currentUser);
        if ($denyView) return $denyView;

        return $this->cannotViewThisGrade($currentUser, $grade)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? Response::allow();
    }

    public function createForColumn(User $currentUser, GradeColumn $gradeColumn, Enrollment $enrollment): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? $this->cannotCreateGradeForColumn($currentUser, $gradeColumn, $enrollment)
            ?? Response::allow();
    }

    public function update(User $currentUser, Grade $grade): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? $this->cannotManageThisGrade($currentUser, $grade)
            ?? Response::allow();
    }

    public function delete(User $currentUser, Grade $grade): Response
    {
        // Solo Developer puede eliminar calificaciones
        if (!$currentUser->isActive() || !$currentUser->isDeveloper()) {
            return Response::deny('Solo los desarrolladores pueden eliminar calificaciones.');
        }

        return Response::allow();
    }
}