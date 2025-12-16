<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Models\Student;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use Illuminate\Support\Facades\DB;

class StoreEnrollmentService
{
    public function handle(Student $student, array $data): Enrollment
    {
        return DB::transaction(function () use ($student, $data) {
            $updates = [];

            // Reactivar estudiante si estaba inactivo
            if (!$student->isActive()) {
                $updates['is_active'] = true;
            }

            // Cambiar situación a Cursando si estaba Sin actividad
            if ($student->situation === StudentSituation::Inactive) {
                $updates['situation'] = StudentSituation::Active;
            }

            if (!empty($updates)) {
                $student->update($updates);
            }

            return Enrollment::create([
                'student_id' => $student->id,
                'section_id' => $data['section_id'],
                'status' => EnrollmentStatus::Active->value,
            ]);
        });
    }
}