<?php

namespace App\Services\SectionSubjectTeacher;

use App\Models\SectionSubjectTeacher;
use Illuminate\Support\Facades\DB;

class DeleteSectionSubjectTeacherService
{
    public function handle(SectionSubjectTeacher $sst): void
    {
        DB::transaction(function () use ($sst) {
            // Verificar que no tenga calificaciones asociadas
            if ($sst->grades()->exists()) {
                throw new \Exception('No se puede eliminar esta asignación porque tiene calificaciones registradas.');
            }

            $sst->delete();
        });
    }
}