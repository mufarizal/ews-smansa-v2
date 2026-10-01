<?php

return [
    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    'api_key' => env('GEMINI_API_KEY'),
    'primary_model' => env('GEMINI_PRIMARY_MODEL', 'gemini-2.5-flash'),
    'fallback_model' => env('GEMINI_FALLBACK_MODEL', 'gemini-2.5-flash-lite'),
    'timeout' => env('AI_TIMEOUT', 30),
];
