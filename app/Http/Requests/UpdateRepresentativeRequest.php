<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepresentativeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the representative first.
        $representative = $this->route('representative');

        // Verify if the representative is an employee.
        $isEmployee = $representative->user->hasRole(['Supervisor', 'Administrador', 'Profesor']);

        // Validation rules.
        $validationRules = [
            'phone'       => ['required', 'string', 'regex:/^[0-9]{9,13}$/', 'max:15'],
            'occupation'  => ['nullable', 'string', 'max:50'],
            'address'     => ['required', 'string'],
            'birth_date'  => ['required', 'date_format:d/m/Y'],
            'document_id' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}$/',
                Rule::unique('representatives', 'document_id')->ignore($representative->id),
            ],
        ];

        // If the representative is not an employee, allow updating sensitive fields.
        if ($isEmployee) {
            $validationRules['name'] = ['nullable', 'string', 'max:100'];
            $validationRules['last_name'] = ['nullable', 'string', 'max:100'];
            $validationRules['email'] = ['nullable', 'string', 'email', 'max:100'];
            $validationRules['sex'] = ['nullable', 'string', 'max:15'];
        } else {
            $validationRules['name'] = ['required', 'string', 'max:100'];
            $validationRules['last_name'] = ['required', 'string', 'max:100'];
            $validationRules['email'] = [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($representative->user_id),
            ];
            $validationRules['sex'] = ['required', 'string', 'max:15'];
        }

        return $validationRules;
    }
}
