<?php

namespace App\Domains\Enrollments\Http\Requests;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Tenancy\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoteEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enrollment = $this->route('enrollment');
        $currentSectionId = $enrollment->section_id;
        $studentId = $enrollment->student_id;

        return [
            'section_id' => [
                'required',
                'uuid',
                TenantExists::in('sections'),
                // No puede ser la misma sección
                Rule::notIn([$currentSectionId]),
                // No puede tener inscripción activa en esa sección
                Rule::unique('enrollments')->where(function ($query) use ($studentId) {
                    return $query->where('student_id', $studentId)
                        ->where('status', EnrollmentStatus::Active->value);
                }),
            ],
        ];
    }

    /**
     * Validaciones adicionales después de las reglas básicas
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $enrollment = $this->route('enrollment');
            $academicPeriod = $enrollment->section->academicPeriod;

            // Validar que el período académico permita promociones
            if (! $academicPeriod->isPromotable()) {
                $validator->errors()->add(
                    'section_id',
                    "El período académico '{$academicPeriod->name}' no permite promociones."
                );

                return;
            }

            $sectionId = $this->input('section_id');

            // After-hooks run even when base rules failed; guard against non-string input.
            if (! is_string($sectionId) || $sectionId === '') {
                return;
            }

            $targetSection = app(SectionRepository::class)->find($sectionId);

            // Unresolved id within the tenant: the tenant-scoped exists rule already reported it.
            if (! $targetSection) {
                return;
            }

            // Validar que la sección destino pertenezca al MISMO período académico
            if ($targetSection->academic_period_id !== $academicPeriod->id) {
                $validator->errors()->add(
                    'section_id',
                    'La sección destino debe pertenecer al mismo período académico.'
                );

                return;
            }

            if ($targetSection->isFull()) {
                $validator->errors()->add('section_id', Section::CAPACITY_FULL_MESSAGE);
            }
        });
    }

    public function attributes(): array
    {
        return [
            'section_id' => 'sección',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'La sección es obligatoria.',
            'section_id.exists' => 'La sección seleccionada no existe.',
            'section_id.not_in' => 'El estudiante ya está en esta sección.',
            'section_id.unique' => 'El estudiante ya tiene una inscripción activa en esta sección.',
        ];
    }
}
