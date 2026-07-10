<?php

namespace App\Domains\Grades\Exceptions;

use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class GradingConfigurationIncompleteException extends RuntimeException implements RenderableDomainException
{
    public static function make(): self
    {
        return new self('La configuración de evaluaciones debe sumar 100% antes de calificar.');
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return 'GRADING_CONFIGURATION_INCOMPLETE';
    }
}
