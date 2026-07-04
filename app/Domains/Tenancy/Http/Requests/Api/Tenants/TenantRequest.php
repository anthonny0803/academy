<?php

namespace App\Domains\Tenancy\Http\Requests\Api\Tenants;

use App\Domains\Shared\Traits\ThrowsApiValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class TenantRequest extends FormRequest
{
    use ThrowsApiValidationException;

    abstract public function rules(): array;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) $this->input('slug')),
        ]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'slug' => 'identificador',
            'plan' => 'plan',
            'status' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'slug.required' => 'El identificador es obligatorio.',
            'slug.max' => 'El identificador no puede superar los 255 caracteres.',
            'slug.unique' => 'Ya existe una organización con este identificador.',
            'plan.in' => 'El plan seleccionado no es válido.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
