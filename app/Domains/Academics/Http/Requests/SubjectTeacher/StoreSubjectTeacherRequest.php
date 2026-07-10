<?php

namespace App\Domains\Academics\Http\Requests\SubjectTeacher;

use App\Domains\Tenancy\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['uuid', TenantExists::in('subjects')],
        ];
    }

    public function messages(): array
    {
        return [
            'subjects.required' => 'Debes seleccionar al menos una materia.',
            'subjects.array' => 'El formato de materias no es válido.',
            'subjects.min' => 'Debes seleccionar al menos una materia.',
            'subjects.*.exists' => 'Una de las materias seleccionadas no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'subjects' => 'materias',
        ];
    }
}
