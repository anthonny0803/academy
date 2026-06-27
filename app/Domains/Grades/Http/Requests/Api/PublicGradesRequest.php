<?php

namespace App\Domains\Grades\Http\Requests\Api;

use App\Domains\Shared\Support\DocumentId;
use App\Domains\Shared\Traits\ThrowsApiValidationException;
use Illuminate\Foundation\Http\FormRequest;

class PublicGradesRequest extends FormRequest
{
    use ThrowsApiValidationException;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->document_id) {
            $this->merge([
                'document_id' => DocumentId::normalize($this->document_id),
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
}
