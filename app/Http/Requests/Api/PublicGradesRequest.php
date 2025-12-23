<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PublicGradesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->document_id) {
            $this->merge([
                'document_id' => strtoupper(preg_replace('/[^A-Z0-9]/i', '', $this->document_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'document_id' => ['required', 'string', 'max:20'],
            'birth_date' => ['required', 'date', 'before:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'document_id' => 'documento de identidad',
            'birth_date' => 'fecha de nacimiento',
        ];
    }

    public function messages(): array
    {
        return [
            'document_id.required' => 'El documento de identidad es obligatorio.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.date' => 'La fecha de nacimiento no es válida.',
            'birth_date.before' => 'La fecha de nacimiento no es válida.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}