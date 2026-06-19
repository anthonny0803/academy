<?php

namespace App\Domains\Academics\Http\Requests\Api\Sections;

use App\Domains\Academics\Http\Requests\Sections\StoreSectionRequest as WebStoreSectionRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreSectionRequest extends WebStoreSectionRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
