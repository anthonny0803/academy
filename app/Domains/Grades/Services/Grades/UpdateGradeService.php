<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Grades\Exceptions\GradeOutOfRangeException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Repositories\GradeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateGradeService
{
    public function __construct(
        private GradeRepository $gradeRepository
    ) {}

    public function handle(Grade $grade, array $data): Grade
    {
        return DB::transaction(function () use ($grade, $data) {
            $sst = $grade->gradeColumn->sectionSubjectTeacher;
            $academicPeriod = $sst->section->academicPeriod;

            // Validar rango de nota
            if (! $academicPeriod->isGradeValid($data['value'])) {
                throw GradeOutOfRangeException::make($academicPeriod->min_grade, $academicPeriod->max_grade);
            }

            // Auditoría simple: registrar cambio
            $oldValue = $grade->value;
            $newValue = $data['value'];

            if ($oldValue != $newValue) {
                Log::info('Grade updated', [
                    'grade_id' => $grade->id,
                    'enrollment_id' => $grade->enrollment_id,
                    'grade_column_id' => $grade->grade_column_id,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'modified_by' => Auth::id(),
                    'modified_at' => now(),
                ]);
            }

            $attributes = [
                'value' => $data['value'],
                'last_modified_by' => Auth::id(),
            ];

            if (array_key_exists('observation', $data)) {
                $attributes['observation'] = $data['observation'];
            }

            $this->gradeRepository->update($grade, $attributes);

            return $grade->fresh(['enrollment.student.user', 'gradeColumn']);
        });
    }
}
