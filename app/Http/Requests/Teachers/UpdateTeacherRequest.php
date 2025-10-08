<?php

namespace App\Http\Requests\Teachers;

use App\Http\Requests\Shared\UpdatePersonRequest;

class UpdateTeacherRequest extends UpdatePersonRequest
{
    protected function getUserId(): int
    {
        return $this->route('teacher')->user_id;
    }
}
