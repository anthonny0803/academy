<?php

namespace App\Domains\Grades\Http\Requests\Grades;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Tenancy\Rules\TenantExists;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BatchGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function getGradeColumn()
    {
        return $this->route('gradeColumn');
    }

    public function rules(): array
    {
        [$minGrade, $maxGrade] = $this->gradeRange();

        return [
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.enrollment_id' => [
                'required',
                'uuid',
                TenantExists::in('enrollments'),
            ],
            'grades.*.value' => [
                'nullable',
                'numeric',
                "min:{$minGrade}",
                "max:{$maxGrade}",
            ],
            'grades.*.observation' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'grades' => 'calificaciones',
            'grades.*.enrollment_id' => 'estudiante',
            'grades.*.value' => 'nota',
            'grades.*.observation' => 'observación',
        ];
    }

    public function messages(): array
    {
        [$minGrade, $maxGrade] = $this->gradeRange();

        return [
            'grades.required' => 'Debe enviar al menos una calificación.',
            'grades.array' => 'El formato de calificaciones no es válido.',
            'grades.*.enrollment_id.required' => 'El estudiante es obligatorio.',
            'grades.*.enrollment_id.exists' => 'El estudiante no existe.',
            'grades.*.value.numeric' => 'La nota debe ser un número.',
            'grades.*.value.min' => "La nota no puede ser menor a {$minGrade}.",
            'grades.*.value.max' => "La nota no puede ser mayor a {$maxGrade}.",
            'grades.*.observation.max' => 'La observación no puede superar los 255 caracteres.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $gradeColumn = $this->getGradeColumn();

            if (! $gradeColumn) {
                $validator->errors()->add('grade_column', 'La columna de evaluación no existe.');

                return;
            }

            $sst = $gradeColumn->sectionSubjectTeacher;

            if (! $sst->isConfigurationComplete()) {
                $remaining = $sst->getRemainingWeight();
                $validator->errors()->add(
                    'configuration',
                    "No puedes calificar hasta completar la configuración. Faltan {$remaining}% para llegar al 100%."
                );
            }

            $this->validateEnrollmentsAreGradable($validator, $sst->section_id);
        });
    }

    private function gradeRange(): array
    {
        $academicPeriod = $this->getGradeColumn()?->sectionSubjectTeacher?->section?->academicPeriod;

        return [
            $academicPeriod?->min_grade ?? 0,
            $academicPeriod?->max_grade ?? 100,
        ];
    }

    private function validateEnrollmentsAreGradable(Validator $validator, string $sectionId): void
    {
        $grades = $this->input('grades', []);

        // After hooks run even when base rules fail: the 'array' rule already reported a scalar.
        if (! is_array($grades)) {
            return;
        }

        $enrollments = Enrollment::query()
            ->whereIn('id', collect($grades)->pluck('enrollment_id')->filter()->unique())
            ->get(['id', 'section_id', 'status'])
            ->keyBy('id');

        foreach ($grades as $index => $grade) {
            $enrollmentId = $grade['enrollment_id'] ?? null;

            // Unresolved id within the tenant: the tenant-scoped exists rule already reported it.
            if (! $enrollmentId || ! $enrollments->has($enrollmentId)) {
                continue;
            }

            $enrollment = $enrollments->get($enrollmentId);

            if ($enrollment->section_id !== $sectionId) {
                $validator->errors()->add(
                    "grades.{$index}.enrollment_id",
                    'El estudiante no pertenece a esta sección.'
                );

                continue;
            }

            if ($enrollment->status !== EnrollmentStatus::Active->value) {
                $validator->errors()->add(
                    "grades.{$index}.enrollment_id",
                    'La inscripción del estudiante no está activa.'
                );
            }
        }
    }
}
