<?php

namespace App\Domains\Shared\Contracts;

interface HasEntityName
{
    public function getEntityName(): string;
}
