<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class SubjectTeacherInUseException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('No se puede eliminar una asignación con registros asociados.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'SUBJECT_TEACHER_IN_USE';
    }
}
