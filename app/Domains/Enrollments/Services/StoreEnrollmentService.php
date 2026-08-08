<?php

namespace App\Domains\Enrollments\Services;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Services\Sections\EnsureSectionHasCapacityService;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Exceptions\StudentAlreadyEnrolledInPeriodException;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Representatives\Services\SyncRepresentativeStatusService;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;

class StoreEnrollmentService
{
    public function __construct(
        private EnsureSectionHasCapacityService $ensureSectionHasCapacity,
        private SyncRepresentativeStatusService $syncRepresentativeStatus,
        private StudentRepository $studentRepository,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function handle(Student $student, array $data): Enrollment
    {
        return DB::transaction(function () use ($student, $data) {
            $this->enrollmentRepository->lockStudentEnrollments($student->id);

            $section = $this->ensureSectionHasCapacity->handle($data['section_id']);

            $this->assertNotAlreadyEnrolledInPeriod($student, $section);

            $updates = [];
            $wasInactive = ! $student->isActive();

            if ($wasInactive) {
                $updates['is_active'] = true;
            }

            if ($student->situation === StudentSituation::Inactive) {
                $updates['situation'] = StudentSituation::Active;
            }

            if (! empty($updates)) {
                $this->studentRepository->update($student, $updates);
            }

            $enrollment = $this->enrollmentRepository->create([
                'student_id' => $student->id,
                'section_id' => $data['section_id'],
                'status' => EnrollmentStatus::Active->value,
            ]);

            // If the student returned to active, sync representative status
            if ($wasInactive) {
                $this->syncRepresentativeStatus->handle($student->representative_id);
            }

            return $enrollment;
        });
    }

    private function assertNotAlreadyEnrolledInPeriod(Student $student, Section $section): void
    {
        $alreadyEnrolled = $this->enrollmentRepository
            ->hasActiveEnrollmentInPeriod($student->id, $section->academic_period_id);

        if (! $alreadyEnrolled) {
            return;
        }

        throw StudentAlreadyEnrolledInPeriodException::forPeriod($section->academicPeriod);
    }
}
