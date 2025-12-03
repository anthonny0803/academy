<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromoteEnrollmentService
{
    /**
     * Promover estudiante a otra sección del MISMO período académico.
     * 
     * IMPORTANTE: "Promover" significa que el estudiante avanza de nivel
     * (ej: 4to grado → 5to grado) DENTRO del mismo período académico.
     * 
     * - Marca la inscripción actual como "promovido"
     * - Crea una nueva inscripción activa en la sección destino
     */
    public function handle(Enrollment $enrollment, int $newSectionId): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $newSectionId) {
            $oldSectionId = $enrollment->section_id;
            $oldSectionName = $enrollment->section->name;

            // Marcar inscripción actual como promovido
            $enrollment->update(['status' => EnrollmentStatus::Promoted->value]);

            // Crear nueva inscripción activa
            $newEnrollment = Enrollment::create([
                'student_id' => $enrollment->student_id,
                'section_id' => $newSectionId,
                'status' => EnrollmentStatus::Active->value,
            ]);

            // Cargar relaciones para el log
            $newEnrollment->load('section');

            // Registrar en log para auditoría
            Log::info('Student promoted to new section', [
                'student_id' => $enrollment->student_id,
                'student_code' => $enrollment->student->student_code,
                'old_enrollment_id' => $enrollment->id,
                'new_enrollment_id' => $newEnrollment->id,
                'old_section_id' => $oldSectionId,
                'old_section_name' => $oldSectionName,
                'new_section_id' => $newSectionId,
                'new_section_name' => $newEnrollment->section->name,
                'academic_period' => $newEnrollment->section->academicPeriod->name,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $newEnrollment->fresh(['student.user', 'section.academicPeriod']);
        });
    }
}