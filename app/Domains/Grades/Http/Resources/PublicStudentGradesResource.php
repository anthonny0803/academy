<?php

namespace App\Domains\Grades\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicStudentGradesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'student' => $this->mapStudent($this['student']),
            'enrollments' => collect($this['enrollments'])
                ->map(fn (array $enrollment) => $this->mapEnrollment($enrollment))
                ->all(),
        ];
    }

    private function mapStudent(array $student): array
    {
        return [
            'code' => $student['code'],
            'name' => $student['name'],
            'situation' => $student['situation'],
            'relationshipType' => $student['relationship_type'],
            'isActive' => $student['is_active'],
        ];
    }

    private function mapEnrollment(array $enrollment): array
    {
        return [
            'academicPeriod' => $enrollment['academic_period'],
            'section' => $enrollment['section'],
            'status' => $enrollment['status'],
            'passed' => $enrollment['passed'],
            'subjects' => collect($enrollment['subjects'])
                ->map(fn (array $subject) => $this->mapSubject($subject))
                ->all(),
        ];
    }

    private function mapSubject(array $subject): array
    {
        return [
            'name' => $subject['name'],
            'teacher' => $subject['teacher'],
            'evaluations' => collect($subject['evaluations'])
                ->map(fn (array $evaluation) => $this->mapEvaluation($evaluation))
                ->all(),
            'weightedAverage' => $subject['weighted_average'],
            'isPassing' => $subject['is_passing'],
        ];
    }

    private function mapEvaluation(array $evaluation): array
    {
        return [
            'name' => $evaluation['name'],
            'weight' => $evaluation['weight'],
            'grade' => $evaluation['grade'],
            'observation' => $evaluation['observation'],
        ];
    }
}
