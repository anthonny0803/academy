<?php

namespace App\Domains\Academics\Http\Requests\SectionSubjectTeacher;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'status' => ['required', Rule::in(SectionSubjectTeacherStatus::toArray())],
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
