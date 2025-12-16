<?php

namespace App\Services\Students;

use App\Models\Student;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawStudentService
{
    public function handle(Student $student, string $reason): array
    {
        return DB::transaction(function () use ($student, $reason) {
            $activeEnrollments = $student->enrollments()
                ->where('status', EnrollmentStatus::Active->value)
                ->with('section.academicPeriod')
                ->get();

            $enrollmentIds = $activeEnrollments->pluck('id')->toArray();
            $enrollmentsCount = $activeEnrollments->count();
            $sectionsInfo = $activeEnrollments->map(fn($e) => [
                'enrollment_id' => $e->id,
                'section' => $e->section->name,
                'academic_period' => $e->section->academicPeriod->name,
            ])->toArray();

            $student->enrollments()
                ->where('status', EnrollmentStatus::Active->value)
                ->update(['status' => EnrollmentStatus::Withdrawn->value]);

            $student->update([
                'is_active' => false,
                'situation' => StudentSituation::Inactive,
            ]);

            Log::info('Student withdrawn from institution', [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'student_name' => $student->user->full_name,
                'enrollments_affected' => $enrollmentIds,
                'sections_info' => $sectionsInfo,
                'reason' => $reason,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return [
                'student' => $student->fresh(['user', 'representative', 'enrollments.section.academicPeriod']),
                'enrollments_withdrawn' => $enrollmentsCount,
            ];
        });
    }
}