<?php

namespace App\Domains\Grades\Policies;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
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
        if (! $this->isActiveForGrades($user)) {
            if ($user->isTeacher()) {
                return Response::deny('Tu perfil de profesor no está activo.');
            }

            return Response::deny('Tu usuario no está activo.');
        }

        if (! $user->isDeveloper()
            && ! $user->isSupervisor()
            && ! $user->isAdmin()
            && ! $user->isTeacher()
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
                ->whereHas('enrollments', fn ($q) => $q->where('id', $grade->enrollment_id))
                ->exists();

            $isOwnGrade = $user->isStudent()
                && $user->student
                && $grade->enrollment->student_id === $user->student->id;

            if (! $isHisStudent && ! $isOwnGrade) {
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

    private function cannotViewThisAssignment(User $user, SectionSubjectTeacher $sectionSubjectTeacher): ?Response
    {
        // Developer, Supervisor, Admin pueden ver cualquier asignación
        if ($user->isDeveloper() || $user->isSupervisor() || $user->isAdmin()) {
            return null;
        }

        // Teacher solo sus asignaciones
        if ($user->isTeacher() && $user->teacher) {
            if ($sectionSubjectTeacher->teacher_id !== $user->teacher->id) {
                return Response::deny('Esta asignación no te corresponde.');
            }

            return null;
        }

        return Response::deny('No tienes autorización para ver estas calificaciones.');
    }

    private function cannotManageGrades(User $user): ?Response
    {
        // Developer siempre puede (si user activo)
        if ($user->isDeveloper()) {
            if (! $user->isActive()) {
                return Response::deny('Tu usuario no está activo.');
            }

            return null;
        }

        // Teacher necesita teacher.is_active
        if ($user->isTeacher()) {
            if (! $user->teacher || ! $user->teacher->isActive()) {
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

        if (! $user->teacher || $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No eres el profesor asignado a esta materia/sección.');
        }

        if ($sst->status !== SectionSubjectTeacherStatus::Active->value) {
            return Response::deny('Esta asignación no está activa.');
        }

        return null;
    }

    private function cannotManageColumn(User $user, GradeColumn $gradeColumn): ?Response
    {
        if ($user->isDeveloper()) {
            return null;
        }

        $sst = $gradeColumn->sectionSubjectTeacher;

        // Verificar que es su asignación
        if (! $user->teacher || $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No puedes calificar en esta asignatura/sección.');
        }

        // Verificar asignación activa
        if ($sst->status !== SectionSubjectTeacherStatus::Active->value) {
            return Response::deny('Esta asignación no está activa.');
        }

        // Verificar que la configuración está completa (suma 100%)
        if (! $sst->isConfigurationComplete()) {
            return Response::deny('La configuración de evaluaciones debe sumar 100% antes de calificar.');
        }

        return null;
    }

    private function cannotCreateGradeForColumn(User $user, GradeColumn $gradeColumn, Enrollment $enrollment): ?Response
    {
        $denyColumn = $this->cannotManageColumn($user, $gradeColumn);
        if ($denyColumn) {
            return $denyColumn;
        }

        $sst = $gradeColumn->sectionSubjectTeacher;

        // Verificar que el estudiante pertenece a esta sección
        if ($enrollment->section_id !== $sst->section_id) {
            return Response::deny('El estudiante no pertenece a esta sección.');
        }

        // Verificar inscripción activa
        if ($enrollment->status !== EnrollmentStatus::Active->value) {
            return Response::deny('La inscripción del estudiante no está activa.');
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
        if ($denyView) {
            return $denyView;
        }

        return $this->cannotViewThisGrade($currentUser, $grade)
            ?? Response::allow();
    }

    public function viewForAssignment(User $currentUser, SectionSubjectTeacher $sectionSubjectTeacher): Response
    {
        return $this->cannotViewGrades($currentUser)
            ?? $this->cannotViewThisAssignment($currentUser, $sectionSubjectTeacher)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? Response::allow();
    }

    public function createBatch(User $currentUser, GradeColumn $gradeColumn): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? $this->cannotManageColumn($currentUser, $gradeColumn)
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
        if (! $currentUser->isActive() || ! $currentUser->isDeveloper()) {
            return Response::deny('Solo los desarrolladores pueden eliminar calificaciones.');
        }

        return Response::allow();
    }
}
