<?php

namespace App\Domains\Identity\Http\Requests\Api\Users;

use App\Domains\Identity\Http\Requests\Users\StoreUserRequest as WebStoreUserRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreUserRequest extends WebStoreUserRequest
{
    use ThrowsApiValidationException;
}
