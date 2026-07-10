<?php

namespace App\Domains\Identity\Exceptions;

use App\Domains\Identity\Enums\Role;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class UnsupportedRoleAssignmentException extends RuntimeException implements RenderableDomainException
{
    public static function make(Role $role): self
    {
        return new self("Rol {$role->value} no soportado para asignación");
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'UNSUPPORTED_ROLE_ASSIGNMENT';
    }
}
