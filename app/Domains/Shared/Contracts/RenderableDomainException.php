<?php

namespace App\Domains\Shared\Contracts;

interface RenderableDomainException
{
    public function statusCode(): int;

    public function errorCode(): string;
}
