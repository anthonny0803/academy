<?php

namespace App\Http\Requests\Enrollments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentSectionId = $this->route('enrollment')->section_id;
        $studentId = $this->route('enrollment')->student_id;

        return [
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
                Rule::notIn([$currentSectionId]),
                Rule::unique('enrollments')->where(function ($query) use ($studentId) {
                    return $query->where('student_id', $studentId)
                        ->where('status', 'activo');
                }),
            ],
            'reason' => ['required', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'section_id' => 'sección',
            'reason' => 'motivo',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'La sección es obligatoria.',
            'section_id.exists' => 'La sección seleccionada no existe.',
            'section_id.not_in' => 'El estudiante ya está en esta sección.',
            'section_id.unique' => 'El estudiante ya tiene una inscripción activa en esta sección.',
            'reason.required' => 'El motivo es obligatorio.',
            'reason.max' => 'El motivo no puede superar los 500 caracteres.',
        ];
    }
}
