<?php

namespace App\Domains\Academics\Enums;

enum SectionSubjectTeacherStatus: string
{
    case Active = 'activo';
    case Inactive = 'inactivo';
    case Substitute = 'suplente';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
