<?php

namespace App\Services\SectionSubjectTeacher;

use App\Models\SectionSubjectTeacher;
use App\Models\SubjectTeacher;
use Illuminate\Support\Facades\DB;

class StoreSectionSubjectTeacherService
{
    public function handle(array $data): SectionSubjectTeacher
    {
        return DB::transaction(function () use ($data) {
            // Verificar que el profesor PUEDE impartir esa materia
            $canTeach = SubjectTeacher::where('teacher_id', $data['teacher_id'])
                ->where('subject_id', $data['subject_id'])
                ->exists();

            if (!$canTeach) {
                throw new \Exception('El profesor seleccionado no está autorizado para impartir esta materia.');
            }

            // Si se marca como principal, despromover al anterior principal (si existe)
            if (isset($data['is_primary']) && $data['is_primary']) {
                SectionSubjectTeacher::where('section_id', $data['section_id'])
                    ->where('subject_id', $data['subject_id'])
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // Crear la asignación
            $sst = SectionSubjectTeacher::create([
                'section_id' => $data['section_id'],
                'subject_id' => $data['subject_id'],
                'teacher_id' => $data['teacher_id'],
                'is_primary' => $data['is_primary'] ?? false,
                'status' => $data['status'],
            ]);

            return $sst->fresh(['section', 'subject', 'teacher']);
        });
    }
}