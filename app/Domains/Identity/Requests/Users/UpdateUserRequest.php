<?php

namespace App\Domains\Identity\Requests\Users;

use App\Shared\Requests\UpdateEmployeeRequest;

class UpdateUserRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): int
    {
        return $this->route('user')->id;
    }
}
