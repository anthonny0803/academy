<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | The text-generation provider the AiTextGenerator port resolves to. Only
    | "anthropic" ships today; this key keeps room for swapping providers behind
    | the same port without touching call sites.
    |
    */

    'default' => env('AI_PROVIDER', 'anthropic'),

    /*
    |--------------------------------------------------------------------------
    | Anthropic (Claude)
    |--------------------------------------------------------------------------
    |
    | Credentials and request defaults for the Anthropic Messages API. The
    | adapter sends `api_key` as the `x-api-key` header and `version` as the
    | `anthropic-version` header. `model` defaults to Claude Opus 4.8. The
    | `max_tokens` ceiling is tuned for short narrative observations.
    |
    */

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
        'version' => env('ANTHROPIC_VERSION', '2023-06-01'),
        'model' => env('ANTHROPIC_MODEL', 'claude-opus-4-8'),
        'max_tokens' => (int) env('ANTHROPIC_MAX_TOKENS', 1024),
        'timeout' => (int) env('ANTHROPIC_TIMEOUT', 30),
    ],

];
