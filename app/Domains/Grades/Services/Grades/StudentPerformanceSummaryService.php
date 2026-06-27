<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Students\Models\Student;

class StudentPerformanceSummaryService
{
    public function forStudent(Student $student): array
    {
        return [
            'student' => [
                'code' => $student->student_code,
                'name' => $student->user->full_name,
                'situation' => $student->situation?->value ?? 'N/A',
                'relationship_type' => $student->relationship_type,
                'is_active' => $student->is_active,
            ],
            'enrollments' => $student->enrollments
                ->map(fn ($enrollment) => $this->buildEnrollmentData($enrollment))
                ->toArray(),
        ];
    }

    private function buildEnrollmentData($enrollment): array
    {
        $passingGrade = $enrollment->section->academicPeriod->passing_grade ?? 60;

        return [
            'academic_period' => $enrollment->section->academicPeriod->name,
            'section' => $enrollment->section->name,
            'status' => $enrollment->status,
            'passed' => $enrollment->passed,
            'subjects' => $enrollment->section->sectionSubjectTeachers
                ->map(fn ($sst) => $this->buildSubjectData($sst, $enrollment, $passingGrade))
                ->toArray(),
        ];
    }

    private function buildSubjectData($sst, $enrollment, float $passingGrade): array
    {
        $evaluations = $sst->gradeColumns->map(function ($column) use ($enrollment) {
            $grade = $enrollment->grades->firstWhere('grade_column_id', $column->id);

            return [
                'name' => $column->name,
                'weight' => (float) $column->weight,
                'grade' => $grade ? (float) $grade->value : null,
                'observation' => $grade?->observation,
            ];
        });

        $weightedAverage = $sst->calculateStudentAverage($enrollment->id);

        return [
            'name' => $sst->subject->name,
            'teacher' => $sst->teacher->user->full_name,
            'evaluations' => $evaluations->toArray(),
            'weighted_average' => $weightedAverage,
            'is_passing' => $weightedAverage !== null ? $weightedAverage >= $passingGrade : null,
        ];
    }
}
