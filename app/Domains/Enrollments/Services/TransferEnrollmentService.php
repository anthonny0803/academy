<?php

namespace App\Domains\Enrollments\Services;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Representatives\Services\SyncRepresentativeStatusService;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Repositories\StudentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferEnrollmentService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus,
        private StudentRepository $studentRepository,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function handle(Enrollment $enrollment, string $reason): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $reason) {
            $student = $enrollment->student;
            $representativeId = $student->representative_id;

            $this->enrollmentRepository->update($enrollment, ['status' => EnrollmentStatus::Transferred->value]);

            Log::info('Student transferred out of institution', [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'enrollment_id' => $enrollment->id,
                'section_id' => $enrollment->section_id,
                'section_name' => $enrollment->section->name,
                'academic_period' => $enrollment->section->academicPeriod->name,
                'reason' => $reason,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            if (! $student->hasActiveEnrollments()) {
                $this->studentRepository->update($student, [
                    'is_active' => false,
                    'situation' => StudentSituation::Inactive,
                ]);

                Log::info('Student deactivated after transfer (no active enrollments)', [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);

                // Synchronize representative status
                $this->syncRepresentativeStatus->handle($representativeId);
            }

            return $enrollment->fresh(['student.user', 'section.academicPeriod']);
        });
    }
}
