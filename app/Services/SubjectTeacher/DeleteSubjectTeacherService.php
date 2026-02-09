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