<?php

namespace App\Domains\Enrollments\Services;

use App\Domains\Enrollments\Exceptions\EnrollmentHasGradesException;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Representatives\Services\SyncRepresentativeStatusService;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Repositories\StudentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteEnrollmentService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus,
        private StudentRepository $studentRepository,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function handle(Enrollment $enrollment): void
    {
        if ($enrollment->grades()->withTrashed()->exists()) {
            throw EnrollmentHasGradesException::make();
        }

        DB::transaction(function () use ($enrollment) {
            $student = $enrollment->student;
            $representativeId = $student->representative_id;
            $enrollmentId = $enrollment->id;
            $sectionName = $enrollment->section->name;

            $this->enrollmentRepository->delete($enrollment);

            if (! $student->hasActiveEnrollments()) {
                $this->studentRepository->update($student, [
                    'is_active' => false,
                    'situation' => StudentSituation::Inactive,
                ]);

                Log::info('Student deactivated after enrollment deletion (no active enrollments)', [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'deleted_enrollment_id' => $enrollmentId,
                    'section_name' => $sectionName,
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);

                // Synchronize representative status
                $this->syncRepresentativeStatus->handle($representativeId);
            }
        });
    }
}
