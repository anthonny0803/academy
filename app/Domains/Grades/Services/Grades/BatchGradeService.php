<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\EnrollmentNotGradableException;
use App\Domains\Grades\Exceptions\GradeOutOfRangeException;
use App\Domains\Grades\Exceptions\GradingConfigurationIncompleteException;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchGradeService
{
    public function __construct(
        private GradeRepository $gradeRepository
    ) {}

    /**
     * @param  array  $gradesData  Array of ['enrollment_id' => x, 'value' => y, 'observation' => z]
     * @return array Summary of operations: ['created' => int, 'updated' => int, 'skipped' => int, 'total' => int]
     */
    public function handle(GradeColumn $gradeColumn, array $gradesData): array
    {
        $gradeColumn->loadMissing('sectionSubjectTeacher.section.academicPeriod');
        $sst = $gradeColumn->sectionSubjectTeacher;

        $this->assertConfigurationIsComplete($sst);
        $this->assertWritingGradesAreInRange($sst->section->academicPeriod, $gradesData);
        $this->assertWritingEnrollmentsAreGradable($sst->section_id, $gradesData);

        return DB::transaction(function () use ($gradeColumn, $gradesData) {
            $created = 0;
            $updated = 0;
            $skipped = 0;
            $userId = Auth::id();

            $existingGrades = $this->gradeRepository
                ->forGradeColumns([$gradeColumn->id])
                ->keyBy('enrollment_id');

            foreach ($gradesData as $gradeData) {
                $enrollmentId = $gradeData['enrollment_id'];
                $value = $gradeData['value'] ?? null;
                $observation = $gradeData['observation'] ?? null;

                if (! $this->hasValue($gradeData)) {
                    $skipped++;

                    continue;
                }

                $existingGrade = $existingGrades->get($enrollmentId);

                if ($existingGrade) {
                    if ((float) $existingGrade->value !== (float) $value
                        || $existingGrade->observation !== $observation
                    ) {
                        $this->gradeRepository->update($existingGrade, [
                            'value' => $value,
                            'observation' => $observation,
                            'last_modified_by' => $userId,
                        ]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $newGrade = $this->gradeRepository->create([
                        'enrollment_id' => $enrollmentId,
                        'grade_column_id' => $gradeColumn->id,
                        'value' => $value,
                        'observation' => $observation,
                        'last_modified_by' => $userId,
                    ]);
                    $existingGrades->put($enrollmentId, $newGrade);
                    $created++;
                }
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'total' => count($gradesData),
            ];
        });
    }

    private function assertConfigurationIsComplete(SectionSubjectTeacher $sst): void
    {
        if (! $sst->isConfigurationComplete()) {
            throw GradingConfigurationIncompleteException::make();
        }
    }

    private function assertWritingGradesAreInRange(AcademicPeriod $academicPeriod, array $gradesData): void
    {
        foreach ($gradesData as $gradeData) {
            if (! $this->hasValue($gradeData)) {
                continue;
            }

            if (! $academicPeriod->isGradeValid($gradeData['value'])) {
                throw GradeOutOfRangeException::make($academicPeriod->min_grade, $academicPeriod->max_grade);
            }
        }
    }

    private function assertWritingEnrollmentsAreGradable(string $sectionId, array $gradesData): void
    {
        $writingEnrollmentIds = collect($gradesData)
            ->filter(fn (array $gradeData) => $this->hasValue($gradeData))
            ->pluck('enrollment_id')
            ->unique();

        if ($writingEnrollmentIds->isEmpty()) {
            return;
        }

        $enrollments = Enrollment::query()
            ->whereIn('id', $writingEnrollmentIds)
            ->get(['id', 'section_id', 'status'])
            ->keyBy('id');

        foreach ($writingEnrollmentIds as $enrollmentId) {
            $enrollment = $enrollments->get($enrollmentId);

            // An id the tenant-scoped query did not resolve is, as far as this
            // section is concerned, not one of its enrollments.
            if (! $enrollment || $enrollment->section_id !== $sectionId) {
                throw EnrollmentNotGradableException::outsideSection();
            }

            if ($enrollment->status !== EnrollmentStatus::Active->value) {
                throw EnrollmentNotGradableException::inactive();
            }
        }
    }

    private function hasValue(array $gradeData): bool
    {
        $value = $gradeData['value'] ?? null;

        return $value !== null && $value !== '';
    }
}
