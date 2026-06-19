<?php

namespace App\Domains\Identity\Http\Requests\Api\Users;

use App\Domains\Identity\Http\Requests\Users\UpdateUserRequest as WebUpdateUserRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends WebUpdateUserRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
