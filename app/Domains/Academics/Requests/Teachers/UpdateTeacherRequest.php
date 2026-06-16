<?php

namespace App\Domains\Academics\Requests\Teachers;

use App\Shared\Requests\UpdateEmployeeRequest;

class UpdateTeacherRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): int
    {
        return $this->route('teacher')->user_id;
    }
}
