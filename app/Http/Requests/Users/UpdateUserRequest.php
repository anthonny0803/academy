<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Shared\UpdateEmployeeRequest;

class UpdateUserRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): int
    {
        return $this->route('user')->id;
    }
}
