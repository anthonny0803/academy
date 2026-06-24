<?php

namespace App\Domains\Academics\Enums;

use App\Domains\Shared\Traits\HasValues;

enum SectionSubjectTeacherStatus: string
{
    use HasValues;

    case Active = 'activo';
    case Inactive = 'inactivo';
    case Substitute = 'suplente';
}
