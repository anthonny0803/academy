<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Models\Student;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use App\Services\Representatives\SyncRepresentativeStatusService;
use Illuminate\Support\Facades\DB;

class StoreEnrollmentService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus
    ) {}

    public function handle(Student $student, array $data): Enrollment
    {
        return DB::transaction(function () use ($student, $data) {
            $updates = [];
            $wasInactive = !$student->isActive();

            if ($wasInactive) {
                $updates['is_active'] = true;
            }

            if ($student->situation === StudentSituation::Inactive) {
                $updates['situation'] = StudentSituation::Active;
            }

            if (!empty($updates)) {
                $student->update($updates);
            }

            $enrollment = Enrollment::create([
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
}