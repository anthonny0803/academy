<?php

namespace App\Services\SubjectTeacher;

use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class DeleteSubjectTeacherService
{
    public function handle(Teacher $teacher, Subject $subject): void
    {
        DB::transaction(function () use ($teacher, $subject) {
            // Verificar que no tenga asignaciones activas en section_subject_teacher
            $hasActiveAssignments = $teacher->sectionSubjectTeachers()
                ->where('subject_id', $subject->id)
                ->where('status', 'activo')
                ->exists();

            if ($hasActiveAssignments) {
                throw new \Exception('No se puede eliminar esta materia porque el profesor tiene asignaciones activas en secciones.');
            }

            $teacher->subjects()->detach($subject->id);
        });
    }
}