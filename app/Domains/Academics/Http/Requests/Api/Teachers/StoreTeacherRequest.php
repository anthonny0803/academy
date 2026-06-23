<?php

namespace App\Domains\Academics\Http\Requests\Api\Teachers;

use App\Domains\Academics\Http\Requests\Teachers\StoreTeacherRequest as WebStoreTeacherRequest;
use App\Domains\Shared\Traits\ThrowsApiValidationException;

class StoreTeacherRequest extends WebStoreTeacherRequest
{
    use ThrowsApiValidationException;
}
