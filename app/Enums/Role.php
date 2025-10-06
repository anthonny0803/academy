<?php

namespace App\Enums;

enum Role: string
{
    case Supervisor = 'Supervisor';
    case Admin = 'Administrador';
    case Teacher = 'Profesor';
    case Representative = 'Representante';
    case Student = 'Estudiante';

    public static function assignableByDeveloper(): array
    {
        return [
            self::Supervisor->value,
            self::Admin->value,
        ];
    }

    public static function assignableBySupervisor(): array
    {
        return [
            self::Admin->value,
        ];
    }

    public static function administrativeRoles(): array
    {
        return [
            self::Supervisor->value,
            self::Admin->value,
        ];
    }

    public static function profileRoles(): array
    {
        return [
            self::Teacher->value,
            self::Representative->value,
            self::Student->value,
        ];
    }
}
