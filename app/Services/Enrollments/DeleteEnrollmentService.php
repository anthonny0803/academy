<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Enums\StudentSituation;
use App\Services\Representatives\SyncRepresentativeStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteEnrollmentService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus
    ) {}

    public function handle(Enrollment $enrollment): void
    {
        DB::transaction(function () use ($enrollment) {
            $student = $enrollment->student;
            $representativeId = $student->representative_id;
            $enrollmentId = $enrollment->id;
            $sectionName = $enrollment->section->name;

            $enrollment->delete();

            if (!$student->hasActiveEnrollments()) {
                $student->update([
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