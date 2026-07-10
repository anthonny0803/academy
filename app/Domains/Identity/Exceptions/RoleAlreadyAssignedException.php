<?php

namespace App\Domains\Identity\Exceptions;

use App\Domains\Identity\Enums\Role;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class RoleAlreadyAssignedException extends RuntimeException implements RenderableDomainException
{
    public static function role(Role $role): self
    {
        return new self("El usuario ya tiene el rol {$role->value}");
    }

    public static function teacherProfile(): self
    {
        return new self('El usuario ya tiene un perfil de profesor');
    }

    public static function representativeProfile(): self
    {
        return new self('El usuario ya tiene un perfil de representante');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'ROLE_ALREADY_ASSIGNED';
    }
}
