<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;

class ConvertToSelfRepresentedRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');

        // Block if already self-represented
        if ($student->user_id === $student->representative?->user_id) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $student = $this->route('student');

            if ($student->age === null || $student->age < 18) {
                $validator->errors()->add(
                    'age',
                    'El estudiante debe ser mayor de edad (18 años o más) para ser auto-representante.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'reason' => 'motivo',
        ];
    }
}