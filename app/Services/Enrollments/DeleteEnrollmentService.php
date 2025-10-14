<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class DeleteEnrollmentService
{
    public function handle(Enrollment $enrollment): void
    {
        DB::transaction(function () use ($enrollment) {
            $enrollment->delete();
        });
    }
}
