<?php

namespace App\Domains\AI\Contracts;

interface AiTextGenerator
{
    public function generate(string $prompt, ?string $system = null): string;
}
