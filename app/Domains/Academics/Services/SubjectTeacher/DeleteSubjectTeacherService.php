<?php

namespace App\Domains\Academics\Services\SubjectTeacher;

use App\Domains\Academics\Exceptions\SubjectTeacherInUseException;
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
                throw SubjectTeacherInUseException::make();
            }

            $teacher->subjects()->detach($subject->id);
        });
    }
}
