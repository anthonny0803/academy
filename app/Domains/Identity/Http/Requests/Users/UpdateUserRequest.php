<?php

namespace App\Domains\Identity\Http\Requests\Users;

use App\Domains\Shared\Http\Requests\UpdateEmployeeRequest;

class UpdateUserRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): string
    {
        return $this->route('user')->id;
    }
}
