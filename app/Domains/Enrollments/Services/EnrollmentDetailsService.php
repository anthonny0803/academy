<?php

namespace App\Domains\Enrollments\Services;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Collection;

class EnrollmentDetailsService
{
    private const DEFAULT_PASSING_GRADE = 60;

    public function handle(Enrollment $enrollment): array
    {
        $enrollment->load([
            'student.user',
            'student.representative.user',
            'section.academicPeriod',
            'section.sectionSubjectTeachers' => fn ($q) => $q->active()->with([
                'subject',
                'teacher.user',
                'gradeColumns' => fn ($q) => $q->orderBy('display_order'),
            ]),
            'grades.gradeColumn',
        ]);

        $passingGrade = $enrollment->section->academicPeriod->passing_grade ?? self::DEFAULT_PASSING_GRADE;

        $subjectsData = $enrollment->section->sectionSubjectTeachers
            ->map(fn (SectionSubjectTeacher $sst) => $this->mapSubject($sst, $enrollment));

        return compact('enrollment', 'subjectsData', 'passingGrade');
    }

    private function mapSubject(SectionSubjectTeacher $sst, Enrollment $enrollment): array
    {
        return [
            'sst_id' => $sst->id,
            'subject_name' => $sst->subject->name,
            'teacher_name' => $sst->teacher->user->full_name,
            'average' => $sst->calculateStudentAverage($enrollment->id),
            'grades_detail' => $this->mapGradesDetail($sst, $enrollment),
        ];
    }

    private function mapGradesDetail(SectionSubjectTeacher $sst, Enrollment $enrollment): Collection
    {
        return $sst->gradeColumns->map(function (GradeColumn $column) use ($enrollment) {
            $grade = $enrollment->grades->firstWhere('grade_column_id', $column->id);

            return [
                'column_name' => $column->name,
                'weight' => $column->weight,
                'value' => $grade?->value,
                'observation' => $grade?->observation,
            ];
        });
    }
}
