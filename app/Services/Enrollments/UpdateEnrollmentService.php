<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class UpdateEnrollmentService
{
    public function handle(Enrollment $enrollment, array $data): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $data) {
            $enrollment->update([
                'status' => $data['status'],
            ]);

            return $enrollment->fresh(['student.user', 'section']);
        });
    }
}
