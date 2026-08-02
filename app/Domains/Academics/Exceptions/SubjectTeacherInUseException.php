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

    /**
     * @param  array<int, string>  $subjectNames
     */
    public static function forSubjects(array $subjectNames): self
    {
        return new self(
            'No puedes quitar estas materias porque el profesor tiene asignaciones activas en ellas: '
            .implode(', ', $subjectNames).'.'
        );
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
