<?php

namespace App\Domains\Students\Http\Requests\Api\Students;

use App\Domains\Shared\Traits\ThrowsApiValidationException;
use App\Domains\Students\Http\Requests\WithdrawStudentRequest as WebWithdrawStudentRequest;

class WithdrawStudentRequest extends WebWithdrawStudentRequest
{
    use ThrowsApiValidationException;
}
