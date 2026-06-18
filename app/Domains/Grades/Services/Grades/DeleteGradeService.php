<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Repositories\GradeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteGradeService
{
    public function __construct(
        private GradeRepository $gradeRepository
    ) {}

    public function handle(Grade $grade): void
    {
        DB::transaction(function () use ($grade) {
            // Auditoría: registrar eliminación
            Log::info('Grade deleted', [
                'grade_id' => $grade->id,
                'enrollment_id' => $grade->enrollment_id,
                'grade_column_id' => $grade->grade_column_id,
                'value' => $grade->value,
                'deleted_by' => Auth::id(),
                'deleted_at' => now(),
            ]);

            // Actualizar quién eliminó antes del soft delete
            $this->gradeRepository->update($grade, ['last_modified_by' => Auth::id()]);

            // Soft delete
            $this->gradeRepository->delete($grade);
        });
    }

    /**
     * Restaurar nota eliminada (solo Developer)
     */
    public function restore(string $gradeId): Grade
    {
        return DB::transaction(function () use ($gradeId) {
            $grade = $this->gradeRepository->findWithTrashed($gradeId);

            Log::info('Grade restored', [
                'grade_id' => $grade->id,
                'restored_by' => Auth::id(),
                'restored_at' => now(),
            ]);

            $this->gradeRepository->restore($grade);
            $this->gradeRepository->update($grade, ['last_modified_by' => Auth::id()]);

            return $grade->fresh(['enrollment.student.user', 'gradeColumn']);
        });
    }
}
