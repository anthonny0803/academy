<?php

namespace App\Domains\Academics\Services\SectionSubjectTeacher;

use App\Domains\Academics\Exceptions\SectionSubjectTeacherHasGradesException;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use Illuminate\Support\Facades\DB;

class DeleteSectionSubjectTeacherService
{
    public function handle(SectionSubjectTeacher $sst): void
    {
        DB::transaction(function () use ($sst) {
            // Verificar que no tenga calificaciones asociadas
            if ($sst->grades()->exists()) {
                throw SectionSubjectTeacherHasGradesException::make();
            }

            $sst->delete();
        });
    }
}
