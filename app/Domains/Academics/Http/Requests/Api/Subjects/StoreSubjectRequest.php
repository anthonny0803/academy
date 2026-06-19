<?php

namespace App\Domains\Academics\Http\Requests\Api\Subjects;

use App\Domains\Academics\Http\Requests\Subjects\StoreSubjectRequest as WebStoreSubjectRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreSubjectRequest extends WebStoreSubjectRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
