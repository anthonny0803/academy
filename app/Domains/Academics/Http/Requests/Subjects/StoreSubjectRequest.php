<?php

namespace App\Domains\Academics\Http\Requests\Subjects;

use App\Domains\Tenancy\Rules\TenantUnique;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', TenantUnique::in('subjects')],
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'description' => 'descripción',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe una asignatura con este nombre.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede superar los 255 caracteres.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('form', 'create') // <- marcamos que falló el modal de creación
        );
    }
}
