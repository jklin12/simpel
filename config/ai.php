<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which AI provider to use for vision and text tasks.
    | Supported: 'claude', 'gemini', 'mock'
    |
    */

    'provider'     => env('AI_PROVIDER', 'claude'),
    'ocr_provider' => env('OCR_PROVIDER'),   // Kosong = ikut 'provider'
    'timeout'      => (int) env('AI_TIMEOUT', 30),

    'providers' => [
        'claude' => [
            'api_key'    => env('CLAUDE_API_KEY'),
            'base_url'   => env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1/messages'),
            'model'      => env('CLAUDE_MODEL', 'claude-haiku-4-5-20251001'),
            'version'    => env('CLAUDE_API_VERSION', '2023-06-01'),
            'max_tokens' => 512,
        ],
        'gemini' => [
            'api_key'    => env('GEMINI_API_KEY'),
            'base_url'   => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
            'model'      => env('GEMINI_MODEL', 'gemini-2.5-flash'),
            'max_tokens' => 1024,
        ],
        'mock' => [],
    ],
];
