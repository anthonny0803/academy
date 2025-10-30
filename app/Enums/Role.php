<?php

namespace App\Enums;

enum Role: string
{
    case Supervisor = 'Supervisor';
    case Admin = 'Administrador';
    case Teacher = 'Profesor';
    case Representative = 'Representante';
    case Student = 'Estudiante';

    // Options for user registration

    public static function administrativeRoles(): array
    {
        return [
            self::Supervisor,
            self::Admin,
        ];
    }

    public static function profileRoles(): array
    {
        return [
            self::Teacher,
            self::Representative,
            self::Student,
        ];
    }

    public static function assignableByDeveloper(): array
    {
        return self::administrativeRoles();
    }

    public static function assignableBySupervisor(): array
    {
        return [self::Admin];
    }

    // Options for role management module
    public static function assignableByDeveloperForAdditionalRoles(): array
    {
        return [
            self::Supervisor,
            self::Admin,
            self::Teacher,
            self::Representative,
        ];
    }

    public static function assignableBySupervisorForAdditionalRoles(): array
    {
        return [
            self::Admin,
            self::Teacher,
            self::Representative,
        ];
    }

    public static function assignableByAdminForAdditionalRoles(): array
    {
        return [
            self::Teacher,
            self::Representative,
        ];
    }
}
