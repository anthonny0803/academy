<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'document_id' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}$/',
                'unique:representatives,document_id',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
            ],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9,13}$/', 'max:15'],
            'occupation' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'sex' => ['required', 'string', 'max:15'],
            'birth_date' => ['required', 'date_format:d/m/Y'],
        ];
    }

    public function messages(): array
    {
        return [
            'document_id.unique'   => 'El documento ya está registrado en otro representante.',
            'email.email'          => 'El correo electrónico no tiene un formato válido.',
            'phone.regex'          => 'El número de teléfono debe tener entre 9 y 13 dígitos.',
            'birth_date.date_format' => 'La fecha de nacimiento debe tener el formato DD/MM/YYYY.',
        ];
    }
}
