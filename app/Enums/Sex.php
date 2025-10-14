<?php

namespace App\Enums;

enum Sex: string
{
    case Male = 'Masculino';
    case Female = 'Femenino';
    case Other = 'Otro';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
