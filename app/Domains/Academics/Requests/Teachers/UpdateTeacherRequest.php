<?php

namespace App\Domains\Academics\Requests\Teachers;

use App\Domains\Shared\Http\Requests\UpdateEmployeeRequest;

class UpdateTeacherRequest extends UpdateEmployeeRequest
{
    protected function getUserId(): int
    {
        return $this->route('teacher')->user_id;
    }
}
