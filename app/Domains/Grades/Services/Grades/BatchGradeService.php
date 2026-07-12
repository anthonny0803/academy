<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\EnrollmentNotGradableException;
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
        $this->assertWritingEnrollmentsAreActive($gradesData);

        return DB::transaction(function () use ($gradeColumn, $gradesData) {
            $created = 0;
            $updated = 0;
            $skipped = 0;
            $userId = Auth::id();

            foreach ($gradesData as $gradeData) {
                $enrollmentId = $gradeData['enrollment_id'];
                $value = $gradeData['value'] ?? null;
                $observation = $gradeData['observation'] ?? null;

                if (! $this->hasValue($gradeData)) {
                    $skipped++;

                    continue;
                }

                $existingGrade = $this->gradeRepository->findByEnrollmentAndColumn($enrollmentId, $gradeColumn->id);

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
                    $this->gradeRepository->create([
                        'enrollment_id' => $enrollmentId,
                        'grade_column_id' => $gradeColumn->id,
                        'value' => $value,
                        'observation' => $observation,
                        'last_modified_by' => $userId,
                    ]);
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

    private function assertWritingEnrollmentsAreActive(array $gradesData): void
    {
        $writingEnrollmentIds = collect($gradesData)
            ->filter(fn (array $gradeData) => $this->hasValue($gradeData))
            ->pluck('enrollment_id')
            ->unique();

        if ($writingEnrollmentIds->isEmpty()) {
            return;
        }

        $hasInactiveEnrollment = Enrollment::query()
            ->whereIn('id', $writingEnrollmentIds)
            ->where('status', '!=', EnrollmentStatus::Active->value)
            ->exists();

        if ($hasInactiveEnrollment) {
            throw EnrollmentNotGradableException::inactive();
        }
    }

    private function hasValue(array $gradeData): bool
    {
        $value = $gradeData['value'] ?? null;

        return $value !== null && $value !== '';
    }
}
