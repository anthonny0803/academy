<?php

namespace App\Services\SubjectTeacher;

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class StoreSubjectTeacherService
{
    public function handle(Teacher $teacher, array $data): void
    {
        DB::transaction(function () use ($teacher, $data) {
            // Sync mantiene solo las materias seleccionadas
            // Elimina las que no están, agrega las nuevas
            $teacher->subjects()->sync($data['subjects'] ?? []);
        });
    }
}