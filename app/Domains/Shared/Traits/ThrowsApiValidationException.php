<?php

namespace App\Domains\Shared\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

trait ThrowsApiValidationException
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
