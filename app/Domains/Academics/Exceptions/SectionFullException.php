<?php

namespace App\Domains\Academics\Exceptions;

use App\Domains\Academics\Models\Section;
use App\Domains\Shared\Contracts\RenderableDomainException;
use RuntimeException;

class SectionFullException extends RuntimeException implements RenderableDomainException
{
    public static function forSection(Section $section): self
    {
        return new self(
            "La sección '{$section->name}' ha alcanzado su capacidad máxima."
        );
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return 'SECTION_FULL';
    }
}
