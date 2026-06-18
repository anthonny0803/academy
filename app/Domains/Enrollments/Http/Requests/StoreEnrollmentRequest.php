<?php

namespace App\Domains\Enrollments\Http\Requests;

use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
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
                'exists:sections,id',
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
