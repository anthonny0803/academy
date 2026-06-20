<?php

namespace App\Domains\Representatives\Http\Requests\Api\Representatives;

use App\Domains\Representatives\Http\Requests\StoreRepresentativeRequest as WebStoreRepresentativeRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreRepresentativeRequest extends WebStoreRepresentativeRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
