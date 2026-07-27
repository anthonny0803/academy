<?php

namespace App\Domains\Academics\Services\AcademicPeriods;

use App\Domains\Academics\Exceptions\AcademicPeriodHasEnrollmentsException;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Academics\Repositories\SectionRepository;
use Illuminate\Support\Facades\DB;

class DeleteAcademicPeriodService
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private AcademicPeriodRepository $academicPeriodRepository
    ) {}

    /**
     * Elimina un período académico y sus secciones inactivas en cascada
     *
     * Precondiciones (validadas en Policy):
     * - El período debe estar activo (no cerrado)
     * - El período no debe tener secciones activas
     *
     * Un período con inscripciones no se elimina: sus calificaciones son historial
     * académico y la FK grades.enrollment_id las borraría físicamente en cascada,
     * saltándose el SoftDeletes de Grade.
     *
     * Si tiene secciones inactivas y vacías, se eliminan junto con sus
     * SectionSubjectTeachers.
     */
    public function handle(AcademicPeriod $academicPeriod): array
    {
        if ($academicPeriod->enrollments()->exists()) {
            throw AcademicPeriodHasEnrollmentsException::forPeriod($academicPeriod);
        }

        return DB::transaction(function () use ($academicPeriod) {
            $deletedSections = 0;
            $deletedAssignments = 0;

            foreach ($academicPeriod->sections as $section) {
                $deletedAssignments += $section->sectionSubjectTeachers()->delete();

                $this->sectionRepository->delete($section);
                $deletedSections++;
            }

            $this->academicPeriodRepository->delete($academicPeriod);

            return [
                'sections_deleted' => $deletedSections,
                'assignments_deleted' => $deletedAssignments,
            ];
        });
    }
}
