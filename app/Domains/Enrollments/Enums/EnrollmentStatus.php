<?php

namespace App\Domains\Enrollments\Enums;

use App\Domains\Shared\Traits\HasValues;

enum EnrollmentStatus: string
{
    use HasValues;

    case Active = 'activo';
    case Completed = 'completado';
    case Withdrawn = 'retirado';
    case Transferred = 'transferido';
    case Promoted = 'promovido';
}
