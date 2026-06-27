<?php

namespace App\Domains\Grades\Services\Api;

use App\Domains\Grades\Services\Grades\StudentPerformanceSummaryService;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;

class PublicGradesService
{
    public function __construct(
        private UserRepository $userRepository,
        private StudentPerformanceSummaryService $summaryService,
    ) {}

    public function getStudentGrades(string $documentId, string $birthDate): ?array
    {
        $user = $this->findUserByCredentials($documentId, $birthDate);

        if (! $user || ! $user->student) {
            return null;
        }

        return $this->summaryService->forStudent($user->student);
    }

    public function getRepresentativeGrades(string $documentId, string $birthDate): ?array
    {
        $user = $this->findUserByCredentials($documentId, $birthDate);

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

    private function findUserByCredentials(string $documentId, string $birthDate): ?User
    {
        return $this->userRepository->findByCredentials($documentId, $birthDate, [
            'student.enrollments.section.academicPeriod',
            'student.enrollments.section.sectionSubjectTeachers' => fn ($q) => $q->with([
                'subject',
                'teacher.user',
                'gradeColumns' => fn ($q) => $q->orderBy('display_order'),
            ]),
            'student.enrollments.grades.gradeColumn',
            'representative.students.user',
            'representative.students.enrollments.section.academicPeriod',
            'representative.students.enrollments.section.sectionSubjectTeachers' => fn ($q) => $q->with([
                'subject',
                'teacher.user',
                'gradeColumns' => fn ($q) => $q->orderBy('display_order'),
            ]),
            'representative.students.enrollments.grades.gradeColumn',
        ]);
    }
}
