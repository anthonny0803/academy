<?php

namespace App\Services\Api;

use App\Models\User;
use App\Models\Student;
use App\Models\Representative;

class PublicGradesService
{
    public function getStudentGrades(string $documentId, string $birthDate): ?array
    {
        $user = $this->findUserByCredentials($documentId, $birthDate);

        if (!$user || !$user->student) {
            return null;
        }

        return $this->buildStudentData($user->student);
    }

    public function getRepresentativeGrades(string $documentId, string $birthDate): ?array
    {
        $user = $this->findUserByCredentials($documentId, $birthDate);

        if (!$user || !$user->representative) {
            return null;
        }

        $representative = $user->representative;

        return [
            'representative' => [
                'name' => $user->full_name,
            ],
            'students' => $representative->students
                ->map(fn($student) => $this->buildStudentData($student))
                ->toArray(),
        ];
    }

    private function findUserByCredentials(string $documentId, string $birthDate): ?User
    {
        return User::where('document_id', $documentId)
            ->whereDate('birth_date', $birthDate)
            ->with([
                'student.enrollments.section.academicPeriod',
                'student.enrollments.section.sectionSubjectTeachers' => fn($q) => $q->with([
                    'subject',
                    'teacher.user',
                    'gradeColumns' => fn($q) => $q->orderBy('display_order'),
                ]),
                'student.enrollments.grades.gradeColumn',
                'representative.students.user',
                'representative.students.enrollments.section.academicPeriod',
                'representative.students.enrollments.section.sectionSubjectTeachers' => fn($q) => $q->with([
                    'subject',
                    'teacher.user',
                    'gradeColumns' => fn($q) => $q->orderBy('display_order'),
                ]),
                'representative.students.enrollments.grades.gradeColumn',
            ])
            ->first();
    }

    private function buildStudentData(Student $student): array
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
                ->map(fn($enrollment) => $this->buildEnrollmentData($enrollment))
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
                ->map(fn($sst) => $this->buildSubjectData($sst, $enrollment, $passingGrade))
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