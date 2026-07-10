<?php

namespace App\Domains\Enrollments\Http\Requests;

use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Tenancy\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id' => [
                'required',
                'uuid',
                TenantExists::in('sections'),
            ],
        ];
    }

    /**
     * Validación adicional: un estudiante no puede inscribirse
     * más de una vez en el mismo período académico
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $sectionId = $this->input('section_id');
            $student = $this->route('student');

            if (! $sectionId || ! $student) {
                return;
            }

            $section = app(SectionRepository::class)->find($sectionId);

            // Unresolved id within the tenant: the tenant-scoped exists rule already reported it.
            if (! $section) {
                return;
            }

            // Verificar si existe una inscripción activa del estudiante en este período
            $existsInPeriod = app(EnrollmentRepository::class)
                ->hasActiveEnrollmentInPeriod($student->id, $section->academic_period_id);

            if ($existsInPeriod) {
                $validator->errors()->add(
                    'section_id',
                    'El estudiante ya tiene una inscripción activa en este período académico.'
                );
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
        ];
    }
}
