<?php

namespace App\Domains\Enrollments\Services;

use App\Domains\Academics\Exceptions\AcademicPeriodNotPromotableException;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Services\Sections\EnsureSectionHasCapacityService;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Exceptions\SectionOutsideAcademicPeriodException;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromoteEnrollmentService
{
    public function __construct(
        private EnsureSectionHasCapacityService $ensureSectionHasCapacity,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    /**
     * Promote a student to another section of the SAME academic period, which
     * means advancing a level (4th grade → 5th grade) without leaving the
     * period: the current enrollment is marked "promovido" and a new active one
     * is created in the target section.
     *
     * The student lock is taken before the section row lock, matching
     * StoreEnrollmentService, so the two write paths can never deadlock against
     * each other by acquiring the same pair in opposite order.
     */
    public function handle(Enrollment $enrollment, string $newSectionId): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $newSectionId) {
            $this->enrollmentRepository->lockStudentEnrollments($enrollment->student_id);

            $academicPeriod = $enrollment->section->academicPeriod;

            $this->assertPeriodAllowsPromotion($academicPeriod);

            $targetSection = $this->ensureSectionHasCapacity->handle($newSectionId);

            $this->assertSectionBelongsToPeriod($targetSection, $academicPeriod);

            $oldSectionId = $enrollment->section_id;
            $oldSectionName = $enrollment->section->name;

            // Marcar inscripción actual como promovido
            $this->enrollmentRepository->update($enrollment, ['status' => EnrollmentStatus::Promoted->value]);

            // Crear nueva inscripción activa
            $newEnrollment = $this->enrollmentRepository->create([
                'student_id' => $enrollment->student_id,
                'section_id' => $newSectionId,
                'status' => EnrollmentStatus::Active->value,
            ]);

            // Cargar relaciones para el log
            $newEnrollment->load('section');

            // Registrar en log para auditoría
            Log::info('Student promoted to new section', [
                'student_id' => $enrollment->student_id,
                'student_code' => $enrollment->student->student_code,
                'old_enrollment_id' => $enrollment->id,
                'new_enrollment_id' => $newEnrollment->id,
                'old_section_id' => $oldSectionId,
                'old_section_name' => $oldSectionName,
                'new_section_id' => $newSectionId,
                'new_section_name' => $newEnrollment->section->name,
                'academic_period' => $newEnrollment->section->academicPeriod->name,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $newEnrollment->fresh(['student.user', 'section.academicPeriod']);
        });
    }

    private function assertPeriodAllowsPromotion(AcademicPeriod $academicPeriod): void
    {
        if ($academicPeriod->isPromotable()) {
            return;
        }

        throw AcademicPeriodNotPromotableException::forPeriod($academicPeriod);
    }

    private function assertSectionBelongsToPeriod(Section $section, AcademicPeriod $academicPeriod): void
    {
        if ($section->academic_period_id === $academicPeriod->id) {
            return;
        }

        throw SectionOutsideAcademicPeriodException::forSection($section, $academicPeriod);
    }
}
