<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class GradeValueNotNumericException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('La nota debe ser un valor numérico.');
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'GRADE_VALUE_NOT_NUMERIC';
    }
}
