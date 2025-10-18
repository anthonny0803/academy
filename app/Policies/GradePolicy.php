<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\User;
use App\Models\SectionSubjectTeacher;
use Illuminate\Auth\Access\Response;

class GradePolicy
{
    // Helper Methods

    private function cannotViewGrades(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver el listado de calificaciones.');
        }

        return null;
    }

    private function cannotViewThisGrade(User $user, Grade $grade): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isSupervisor() && !$user->isAdmin())) {
            return Response::deny('No tienes autorización para ver esta calificación.');
        }

        if ($user->isTeacher()) {
            if ($grade->sectionSubjectTeacher->teacher_id !== $user->teacher->id) {
                return Response::deny('Esta calificación no corresponde a tu asignación.');
            }

            return null;
        }

        if ($user->isRepresentative()) {
            $isHisStudent = $user->representative->students()
                ->whereHas('enrollments', fn($q) => $q->where('id', $grade->enrollment_id))
                ->exists();

            $isOwnGrade = $user->isStudent() && $grade->enrollment->student_id === $user->student->id;

            if (!$isHisStudent && !$isOwnGrade) {
                return Response::deny('Esta calificación no te corresponde a ti ni a tus estudiantes.');
            }

            return null;
        }

        if ($user->isStudent()) {
            if ($grade->enrollment->student_id !== $user->student->id) {
                return Response::deny('Esta calificación no es tuya.');
            }

            return null;
        }

        return Response::deny('No tienes autorización para ver esta calificación.');
    }

    private function cannotManageGrades(User $user): ?Response
    {
        if (!$user->isActive() || (!$user->isDeveloper() && !$user->isTeacher())) {
            return Response::deny('No tienes autorización para gestionar calificaciones.');
        }

        return null;
    }

    private function cannotModifyThisGrade(User $user, Grade $grade): ?Response
    {
        if ($user->isDeveloper()) {
            return null;
        }

        if ($user->isTeacher()) {
            $sst = $grade->sectionSubjectTeacher;

            if ($sst->teacher_id !== $user->teacher->id) {
                return Response::deny('No eres el profesor asignado a esta materia/sección.');
            }

            if ($sst->status !== 'activo') {
                return Response::deny('Esta asignación no está activa.');
            }

            return null;
        }

        return Response::deny('No tienes autorización para modificar esta calificación.');
    }

    private function cannotCreateGradeForAssignment(User $user, int $sectionSubjectTeacherId, int $enrollmentId): ?Response
    {
        if ($user->isDeveloper()) {
            return null;
        }

        if (!$user->isTeacher()) {
            return Response::deny('Solo los profesores pueden calificar.');
        }

        $sst = SectionSubjectTeacher::find($sectionSubjectTeacherId);

        if (!$sst || $sst->teacher_id !== $user->teacher->id) {
            return Response::deny('No puedes calificar en esta asignatura/sección.');
        }

        if ($sst->status !== 'activo') {
            return Response::deny('Esta asignación no está activa.');
        }

        $enrollment = Enrollment::find($enrollmentId);

        if (!$enrollment || $enrollment->section_id !== $sst->section_id) {
            return Response::deny('El estudiante no pertenece a esta sección.');
        }

        if ($enrollment->status !== 'activo') {
            return Response::deny('La inscripción del estudiante no está activa.');
        }

        return null;
    }

    // Policy Methods

    public function viewAny(User $currentUser): Response
    {
        return $this->cannotViewGrades($currentUser)
            ?? Response::allow();
    }

    public function view(User $currentUser, Grade $grade): Response
    {
        return $this->cannotViewThisGrade($currentUser, $grade)
            ?? Response::allow();
    }

    public function create(User $currentUser): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? Response::allow();
    }

    public function createForAssignment(User $currentUser, int $sectionSubjectTeacherId, int $enrollmentId): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? $this->cannotCreateGradeForAssignment($currentUser, $sectionSubjectTeacherId, $enrollmentId)
            ?? Response::allow();
    }

    public function update(User $currentUser, Grade $grade): Response
    {
        return $this->cannotManageGrades($currentUser)
            ?? $this->cannotModifyThisGrade($currentUser, $grade)
            ?? Response::allow();
    }

    public function delete(User $currentUser): Response
    {
        if (!$currentUser->isActive() || !$currentUser->isDeveloper()) {
            return Response::deny('Solo los desarrolladores pueden eliminar calificaciones.');
        }

        return Response::allow();
    }
}