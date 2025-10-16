<?php

namespace App\Services\Enrollments;

use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UpdateEnrollmentService
{
    public function handle(Enrollment $enrollment, array $data): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $data) {
            $changes = [];

            // Si cambia el estado
            if (isset($data['status']) && $data['status'] !== $enrollment->status) {
                $changes['status'] = [
                    'from' => $enrollment->status,
                    'to' => $data['status'],
                ];
            }

            // Si cambia la sección (corrección de error)
            if (isset($data['section_id']) && $data['section_id'] && $data['section_id'] != $enrollment->section_id) {
                $changes['section'] = [
                    'from' => $enrollment->section_id,
                    'to' => $data['section_id'],
                ];
            }

            // Actualizar enrollment
            $updateData = [];
            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }
            if (isset($data['section_id']) && $data['section_id']) {
                $updateData['section_id'] = $data['section_id'];
            }

            if (!empty($updateData)) {
                $enrollment->update($updateData);
            }

            // Auditoría
            if (!empty($changes)) {
                Log::info('Enrollment updated (correction)', [
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $enrollment->student_id,
                    'student_code' => $enrollment->student->student_code,
                    'changes' => $changes,
                    'reason' => $data['reason'] ?? 'No reason provided',
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);
            }

            return $enrollment->fresh(['student.user', 'section.academicPeriod']);
        });
    }
}
