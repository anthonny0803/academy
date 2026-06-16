<?php

namespace App\Domains\Identity\Requests\Users;

use App\Domains\Shared\Http\Requests\UpdateEmployeeRequest;

class UpdateUserRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): int
    {
        return $this->route('user')->id;
    }
}
