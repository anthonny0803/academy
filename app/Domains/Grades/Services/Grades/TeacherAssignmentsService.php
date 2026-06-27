<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use Illuminate\Support\Collection;

class TeacherAssignmentsService
{
    public function handle(Teacher $teacher): Collection
    {
        return SectionSubjectTeacher::forTeacher($teacher->id)
            ->active()
            ->with([
                'section.academicPeriod',
                'subject',
                'gradeColumns',
            ])
            ->get()
            ->groupBy(fn (SectionSubjectTeacher $sst) => $sst->section->academicPeriod->name);
    }
}
