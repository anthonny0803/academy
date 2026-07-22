<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Support\GradeMatrix;
use App\Domains\Students\Models\Student;

class StudentPerformanceSummaryService
{
    private const DEFAULT_PASSING_GRADE = 60;

    public function forStudent(Student $student): array
    {
        $gradeMatrix = GradeMatrix::fromGrades(
            $student->enrollments->flatMap(fn (Enrollment $enrollment) => $enrollment->grades)
        );

        return [
            'student' => [
                'code' => $student->student_code,
                'name' => $student->user->full_name,
                'situation' => $student->situation?->value ?? 'N/A',
                'relationship_type' => $student->relationship_type,
                'is_active' => $student->is_active,
            ],
            'enrollments' => $student->enrollments
                ->map(fn (Enrollment $enrollment) => $this->buildEnrollmentData($enrollment, $gradeMatrix))
                ->toArray(),
        ];
    }

    private function buildEnrollmentData(Enrollment $enrollment, GradeMatrix $gradeMatrix): array
    {
        $passingGrade = $enrollment->section->academicPeriod->passing_grade ?? self::DEFAULT_PASSING_GRADE;

        return [
            'academic_period' => $enrollment->section->academicPeriod->name,
            'section' => $enrollment->section->name,
            'status' => $enrollment->status,
            'passed' => $enrollment->passed,
            'subjects' => $enrollment->section->sectionSubjectTeachers
                ->map(fn (SectionSubjectTeacher $sst) => $this->buildSubjectData($sst, $enrollment, $passingGrade, $gradeMatrix))
                ->toArray(),
        ];
    }

    private function buildSubjectData(SectionSubjectTeacher $sst, Enrollment $enrollment, float $passingGrade, GradeMatrix $gradeMatrix): array
    {
        $evaluations = $sst->gradeColumns->map(function (GradeColumn $column) use ($enrollment, $gradeMatrix) {
            $grade = $gradeMatrix->find($enrollment->id, $column->id);

            return [
                'name' => $column->name,
                'weight' => (float) $column->weight,
                'grade' => $grade ? (float) $grade->value : null,
                'observation' => $grade?->observation,
            ];
        });

        $weightedAverage = $gradeMatrix->weightedAverage($enrollment->id, $sst->gradeColumns);

        return [
            'name' => $sst->subject->name,
            'teacher' => $sst->teacher->user->full_name,
            'evaluations' => $evaluations->toArray(),
            'weighted_average' => $weightedAverage,
            'is_passing' => $weightedAverage !== null ? $weightedAverage >= $passingGrade : null,
        ];
    }
}
