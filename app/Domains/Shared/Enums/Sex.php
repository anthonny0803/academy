<?php

namespace App\Domains\Shared\Enums;

use App\Domains\Shared\Traits\HasValues;

enum Sex: string
{
    use HasValues;

    case Male = 'Masculino';
    case Female = 'Femenino';
    case Other = 'Otro';
}
