<?php

namespace App\Domains\Identity\Http\Requests\Api\Users;

use App\Domains\Identity\Http\Requests\Users\StoreUserRequest as WebStoreUserRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreUserRequest extends WebStoreUserRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }
}
