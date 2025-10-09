<?php

namespace App\Http\Requests\Teachers;

use App\Http\Requests\Shared\UpdateEmployeeRequest;

class UpdateTeacherRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): int
    {
        return $this->route('teacher')->user_id;
    }
}
