<?php

namespace App\Domains\Academics\Services\AcademicPeriods;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Repositories\SectionRepository;
use Illuminate\Support\Facades\DB;

class DeleteAcademicPeriodService
{
    public function __construct(
        private SectionRepository $sectionRepository
    ) {}

    /**
     * Elimina un período académico y sus secciones inactivas en cascada
     *
     * Precondiciones (validadas en Policy):
     * - El período debe estar activo (no cerrado)
     * - El período no debe tener secciones activas
     *
     * Si tiene secciones inactivas, se eliminan en cascada junto con:
     * - Enrollments de esas secciones
     * - SectionSubjectTeachers de esas secciones
     */
    public function handle(AcademicPeriod $academicPeriod): array
    {
        return DB::transaction(function () use ($academicPeriod) {
            $deletedSections = 0;
            $deletedEnrollments = 0;
            $deletedAssignments = 0;

            // Si tiene secciones inactivas, eliminarlas en cascada
            if ($academicPeriod->hasSections()) {
                foreach ($academicPeriod->sections as $section) {
                    // Contar antes de eliminar
                    $deletedEnrollments += $section->enrollments()->count();
                    $deletedAssignments += $section->sectionSubjectTeachers()->count();

                    // Eliminar relaciones de la sección
                    $section->enrollments()->delete();
                    $section->sectionSubjectTeachers()->delete();

                    // Eliminar la sección
                    $this->sectionRepository->delete($section);
                    $deletedSections++;
                }
            }

            // Eliminar el período
            $academicPeriod->delete();

            return [
                'sections_deleted' => $deletedSections,
                'enrollments_deleted' => $deletedEnrollments,
                'assignments_deleted' => $deletedAssignments,
            ];
        });
    }
}
