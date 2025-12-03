<?php

namespace App\Http\Requests\Enrollments;

use App\Models\Enrollment;
use App\Models\Section;
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
                'integer',
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

            if (!$sectionId || !$student) {
                return;
            }

            $section = Section::find($sectionId);

            if (!$section) {
                return;
            }

            // Verificar si existe CUALQUIER inscripción del estudiante en este período
            $existsInPeriod = Enrollment::where('student_id', $student->id)
                ->whereHas('section', fn($q) => $q->where('academic_period_id', $section->academic_period_id))
                ->exists();

            if ($existsInPeriod) {
                $validator->errors()->add(
                    'section_id',
                    'El estudiante ya tiene una inscripción en este período académico.'
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