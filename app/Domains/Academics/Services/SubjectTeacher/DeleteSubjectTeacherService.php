<?php

namespace App\Domains\Academics\Services\SubjectTeacher;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use Illuminate\Support\Facades\DB;

class DeleteSubjectTeacherService
{
    public function handle(Teacher $teacher, Subject $subject): void
    {
        DB::transaction(function () use ($teacher, $subject) {
            $hasAssociatedRecords = $teacher->sectionSubjectTeachers()
                ->where('subject_id', $subject->id)
                ->exists();

            if ($hasAssociatedRecords) {
                throw new \Exception('No se puede eliminar una asignación con registros asociados.');
            }

            $teacher->subjects()->detach($subject->id);
        });
    }
}
