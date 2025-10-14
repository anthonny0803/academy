<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Models\Student;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\DB;

class StoreEnrollmentService
{
    public function handle(Student $student, array $data): Enrollment
    {
        return DB::transaction(function () use ($student, $data) {
            return Enrollment::create([
                'student_id' => $student->id,
                'section_id' => $data['section_id'],
                'status' => EnrollmentStatus::Active->value,
            ]);
        });
    }
}
