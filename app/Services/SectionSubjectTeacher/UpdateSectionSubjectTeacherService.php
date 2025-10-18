<?php

namespace App\Services\SectionSubjectTeacher;

use App\Models\SectionSubjectTeacher;
use Illuminate\Support\Facades\DB;

class UpdateSectionSubjectTeacherService
{
    public function handle(SectionSubjectTeacher $sst, array $data): SectionSubjectTeacher
    {
        return DB::transaction(function () use ($sst, $data) {
            // Si se marca como principal, despromover al anterior principal (si existe)
            if (isset($data['is_primary']) && $data['is_primary']) {
                SectionSubjectTeacher::where('section_id', $sst->section_id)
                    ->where('subject_id', $sst->subject_id)
                    ->where('id', '!=', $sst->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // Actualizar la asignación
            $sst->update([
                'is_primary' => $data['is_primary'] ?? $sst->is_primary,
                'status' => $data['status'],
            ]);

            return $sst->fresh(['section', 'subject', 'teacher']);
        });
    }
}