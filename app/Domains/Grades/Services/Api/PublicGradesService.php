<?php

namespace App\Domains\Grades\Services\Api;

use App\Domains\Grades\Services\Grades\StudentPerformanceSummaryService;
use App\Domains\Grades\Support\StudentPerformanceRelations;
use App\Domains\Identity\Repositories\UserRepository;

class PublicGradesService
{
    public function __construct(
        private UserRepository $userRepository,
        private StudentPerformanceSummaryService $summaryService,
    ) {}

    public function getStudentGrades(string $documentId, string $birthDate): ?array
    {
        $user = $this->userRepository->findByCredentials($documentId, $birthDate, $this->studentRelations());

        if (! $user || ! $user->student) {
            return null;
        }

        return $this->summaryService->forStudent($user->student);
    }

    public function getRepresentativeGrades(string $documentId, string $birthDate): ?array
    {
        $user = $this->userRepository->findByCredentials($documentId, $birthDate, $this->representativeRelations());

        if (! $user || ! $user->representative) {
            return null;
        }

        $representative = $user->representative;

        return [
            'representative' => [
                'name' => $user->full_name,
            ],
            'students' => $representative->students
                ->map(fn ($student) => $this->summaryService->forStudent($student))
                ->toArray(),
        ];
    }

    private function studentRelations(): array
    {
        return StudentPerformanceRelations::prefixed('student');
    }

    private function representativeRelations(): array
    {
        return StudentPerformanceRelations::prefixed('representative.students');
    }
}
