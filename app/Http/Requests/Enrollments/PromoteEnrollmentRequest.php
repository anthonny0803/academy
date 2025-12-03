<?php

namespace App\Http\Requests\Enrollments;

use App\Models\Enrollment;
use App\Models\Section;
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
        $currentAcademicPeriodId = $enrollment->section->academic_period_id;

        return [
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
                // No puede ser la misma sección
                Rule::notIn([$currentSectionId]),
                // No puede tener inscripción activa en esa sección
                Rule::unique('enrollments')->where(function ($query) use ($studentId) {
                    return $query->where('student_id', $studentId)
                        ->where('status', 'activo');
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
            if (!$academicPeriod->isPromotable()) {
                $validator->errors()->add(
                    'section_id',
                    "El período académico '{$academicPeriod->name}' no permite promociones."
                );
                return;
            }

            // Validar que la sección destino pertenezca al MISMO período académico
            $sectionId = $this->input('section_id');
            if ($sectionId) {
                $targetSection = Section::find($sectionId);
                
                if ($targetSection && $targetSection->academic_period_id !== $academicPeriod->id) {
                    $validator->errors()->add(
                        'section_id',
                        'La sección destino debe pertenecer al mismo período académico.'
                    );
                }
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