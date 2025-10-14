<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Active = 'activo';
    case Completed = 'completado';
    case Withdrawn = 'retirado';
    case Transferred = 'transferido';
    case Promoted = 'promovido';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
