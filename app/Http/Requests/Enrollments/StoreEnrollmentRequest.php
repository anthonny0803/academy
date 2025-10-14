<?php

namespace App\Http\Requests\Enrollments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')->id;

        return [
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
                Rule::unique('enrollments')->where(function ($query) use ($studentId) {
                    return $query->where('student_id', $studentId);
                }),
            ],
        ];
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
            'section_id.unique' => 'El estudiante ya está inscrito en esta sección.',
        ];
    }
}
