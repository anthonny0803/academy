<?php

namespace App\Domains\Academics\Services\AcademicPeriods;

use App\Domains\Academics\Exceptions\AcademicPeriodClosedException;
use App\Domains\Academics\Exceptions\AcademicPeriodHasActiveSectionsException;
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
     * Un período cerrado, con secciones activas o con inscripciones no se
     * elimina. Las calificaciones son historial académico y la FK
     * grades.enrollment_id las borraría físicamente en cascada, saltándose el
     * SoftDeletes de Grade.
     *
     * Si tiene secciones inactivas y vacías, se eliminan junto con sus
     * SectionSubjectTeachers.
     */
    public function handle(AcademicPeriod $academicPeriod): array
    {
        if (! $academicPeriod->isActive()) {
            throw AcademicPeriodClosedException::forPeriod($academicPeriod);
        }

        if ($academicPeriod->hasActiveSections()) {
            throw AcademicPeriodHasActiveSectionsException::forPeriod($academicPeriod);
        }

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
