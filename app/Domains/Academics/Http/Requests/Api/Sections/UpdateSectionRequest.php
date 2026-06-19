<?php

namespace App\Domains\Academics\Http\Requests\Api\Sections;

use App\Domains\Academics\Http\Requests\Sections\UpdateSectionRequest as WebUpdateSectionRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateSectionRequest extends WebUpdateSectionRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
