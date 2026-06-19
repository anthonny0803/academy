<?php

namespace App\Domains\Academics\Http\Requests\Teachers;

use App\Domains\Shared\Http\Requests\UpdateEmployeeRequest;

class UpdateTeacherRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): string
    {
        return $this->route('teacher')->user_id;
    }
}
