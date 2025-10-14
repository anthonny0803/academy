<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferEnrollmentService
{
    public function handle(Enrollment $enrollment, int $newSectionId, string $reason): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $newSectionId, $reason) {
            $oldSectionId = $enrollment->section_id;
            $enrollment->update(['status' => EnrollmentStatus::Transferred->value]);
            $newEnrollment = Enrollment::create([
                'student_id' => $enrollment->student_id,
                'section_id' => $newSectionId,
                'status' => EnrollmentStatus::Active->value,
            ]);

            Log::info('Student enrollment transfer', [
                'student_id' => $enrollment->student_id,
                'old_enrollment_id' => $enrollment->id,
                'new_enrollment_id' => $newEnrollment->id,
                'old_section_id' => $oldSectionId,
                'new_section_id' => $newSectionId,
                'reason' => $reason,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $newEnrollment->fresh(['student.user', 'section']);
        });
    }
}
