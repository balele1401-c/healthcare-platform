<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configured for HealthCare Mobile (Flutter), Vercel Staging (*.vercel.app),
    | and local development environments.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => env('APP_ENV') === 'production'
        ? array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'https://*.vercel.app,http://localhost:3000,http://localhost:5173,http://127.0.0.1:8000,http://localhost:8000'))))
        : ['*'],

    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
        '#^http://localhost:\d+$#',
        '#^http://127\.0\.0\.1:\d+$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
