<?php

namespace App\Domains\Identity\Http\Requests\Api\Users;

use App\Domains\Identity\Http\Requests\Users\UpdateUserRequest as WebUpdateUserRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class UpdateUserRequest extends WebUpdateUserRequest
{
    use ThrowsApiValidationException;
}
