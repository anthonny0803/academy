<?php

namespace App\Domains\Academics\Services\SectionSubjectTeacher;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use Illuminate\Support\Facades\DB;

class UpdateSectionSubjectTeacherService
{
    public function handle(SectionSubjectTeacher $sst, array $data): SectionSubjectTeacher
    {
        return DB::transaction(function () use ($sst, $data) {
            // Si se marca como principal, despromover al anterior principal (si existe)
            if (isset($data['is_primary']) && $data['is_primary']) {
                SectionSubjectTeacher::primaryFor($sst->section_id, $sst->subject_id, $sst->id)
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
