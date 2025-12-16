<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferEnrollmentService
{
    public function handle(Enrollment $enrollment, string $reason): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $reason) {
            $student = $enrollment->student;

            // Cambiar status a transferido
            $enrollment->update(['status' => EnrollmentStatus::Transferred->value]);

            // Registrar en log para auditoría
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

            // Desactivar estudiante si no tiene más inscripciones activas
            if (!$student->hasActiveEnrollments()) {
                $student->update([
                    'is_active' => false,
                    'situation' => StudentSituation::Inactive,
                ]);

                Log::info('Student deactivated after transfer (no active enrollments)', [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);
            }

            return $enrollment->fresh(['student.user', 'section.academicPeriod']);
        });
    }
}