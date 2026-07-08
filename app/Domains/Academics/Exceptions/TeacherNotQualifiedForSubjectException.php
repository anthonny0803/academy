<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class TeacherNotQualifiedForSubjectException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('El profesor seleccionado no está autorizado para impartir esta materia.');
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'TEACHER_NOT_QUALIFIED_FOR_SUBJECT';
    }
}
