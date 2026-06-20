<?php

namespace App\Domains\Representatives\Http\Requests\Api\Representatives;

use App\Domains\Representatives\Http\Requests\UpdateRepresentativeRequest as WebUpdateRepresentativeRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateRepresentativeRequest extends WebUpdateRepresentativeRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
