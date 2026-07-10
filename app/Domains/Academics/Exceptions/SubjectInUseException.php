<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class SubjectInUseException extends RuntimeException implements RenderableDomainException
{
    public static function withSectionAssignments(): self
    {
        return new self('No se puede eliminar una asignatura con asignaciones en secciones.');
    }

    public static function withAssignedTeachers(): self
    {
        return new self('No se puede eliminar una asignatura con profesores asignados.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'SUBJECT_IN_USE';
    }
}
