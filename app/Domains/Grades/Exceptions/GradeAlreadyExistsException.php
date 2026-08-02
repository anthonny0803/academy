<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class GradeAlreadyExistsException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('Este estudiante ya tiene una nota en esta evaluación.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'GRADE_ALREADY_EXISTS';
    }
}
