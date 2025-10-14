<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromoteEnrollmentService
{
    public function handle(Enrollment $enrollment, int $newSectionId): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $newSectionId) {
            $oldSectionId = $enrollment->section_id;
            $enrollment->update(['status' => EnrollmentStatus::Promoted->value]);
            $newEnrollment = Enrollment::create([
                'student_id' => $enrollment->student_id,
                'section_id' => $newSectionId,
                'status' => EnrollmentStatus::Active->value,
            ]);

            Log::info('Student enrollment promotion', [
                'student_id' => $enrollment->student_id,
                'old_enrollment_id' => $enrollment->id,
                'new_enrollment_id' => $newEnrollment->id,
                'old_section_id' => $oldSectionId,
                'new_section_id' => $newSectionId,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $newEnrollment->fresh(['student.user', 'section']);
        });
    }
}
