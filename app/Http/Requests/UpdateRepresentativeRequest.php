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
        // Get the representative being updated from the route
        $representative = $this->route('representative');

        return [
            'name'        => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'document_id' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}$/',
                // Ignore self document ID.
                Rule::unique('representatives', 'document_id')->ignore($representative->id),
            ],
            'email'       => [
                'required',
                'string',
                'email',
                'max:100',
                // Ignore self email.
                Rule::unique('users', 'email')->ignore($representative->user_id),
            ],
            'phone'       => ['required', 'string', 'regex:/^[0-9]{9,13}$/', 'max:15'],
            'occupation'  => ['nullable', 'string', 'max:50'],
            'address'     => ['required', 'string'],
            'sex'         => ['required', 'string', 'max:15'],
            'birth_date'  => ['required', 'date_format:d/m/Y'],
        ];
    }
}
