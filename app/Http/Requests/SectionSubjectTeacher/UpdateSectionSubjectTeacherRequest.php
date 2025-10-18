<?php

namespace App\Http\Requests\SectionSubjectTeacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionSubjectTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_primary' => ['nullable', 'boolean'],
            'status' => ['required', 'in:activo,suplente,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_primary.boolean' => 'El campo profesor principal debe ser verdadero o falso.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: activo, suplente o inactivo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'is_primary' => 'profesor principal',
            'status' => 'estado',
        ];
    }
}