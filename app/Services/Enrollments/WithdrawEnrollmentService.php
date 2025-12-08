<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawEnrollmentService
{
    /**
     * Retirar estudiante de la inscripción.
     * 
     * IMPORTANTE: "Retirar" significa que el estudiante abandona o es expulsado.
     * Permanece en el sistema pero ya no está activo en esta inscripción.
     * 
     * Casos de uso:
     * - Abandono voluntario del estudiante
     * - Expulsión disciplinaria
     * - Estudiante que nunca asistió
     * - Problemas de salud que impiden continuar
     */
    public function handle(Enrollment $enrollment, string $reason): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $reason) {
            // Cambiar status a retirado
            $enrollment->update(['status' => EnrollmentStatus::Withdrawn->value]);

            // Registrar en log para auditoría
            Log::info('Student withdrawn from enrollment', [
                'student_id' => $enrollment->student_id,
                'student_code' => $enrollment->student->student_code,
                'student_name' => $enrollment->student->user->full_name,
                'enrollment_id' => $enrollment->id,
                'section_id' => $enrollment->section_id,
                'section_name' => $enrollment->section->name,
                'academic_period' => $enrollment->section->academicPeriod->name,
                'reason' => $reason,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $enrollment->fresh(['student.user', 'section.academicPeriod']);
        });
    }
}