<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "sandbox", "mayar", "komerce"
    |
    */

    'default' => env('PAYMENT_PROVIDER', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Payment Environment Mode
    |--------------------------------------------------------------------------
    |
    | Supported: "sandbox", "test", "production"
    |
    */

    'mode' => env('PAYMENT_MODE', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Test Payment Amount Override (Development / Sandbox ONLY)
    |--------------------------------------------------------------------------
    |
    | For local testing purposes. Ignored in production.
    |
    */

    'test_amount' => env('PAYMENT_MODE') === 'production' ? null : env('TEST_PAYMENT_AMOUNT', null),

    /*
    |--------------------------------------------------------------------------
    | Default Currency & Expiration
    |--------------------------------------------------------------------------
    */

    'currency' => env('PAYMENT_CURRENCY', 'USD'),

    'expiration_minutes' => (int) env('PAYMENT_EXPIRATION_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Provider Specific Credentials & Settings
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'sandbox' => [
            'driver' => 'sandbox',
            'webhook_secret' => env('SANDBOX_WEBHOOK_SECRET', 'sandbox_webhook_secret_key_12345'),
        ],

        'mayar' => [
            'driver' => 'mayar',
            'api_key' => env('MAYAR_API_KEY'),
            'base_url' => env('MAYAR_BASE_URL', 'https://api.mayar.id/hl/v1'),
            'webhook_secret' => env('MAYAR_WEBHOOK_SECRET'),
            'publishable_key' => env('MAYAR_PUBLISHABLE_KEY'),
        ],

        'komerce' => [
            'driver' => 'komerce',
            'api_key' => env('KOMERCE_API_KEY'),
            'base_url' => env('KOMERCE_BASE_URL', 'https://api.komerce.id/v1'),
            'webhook_secret' => env('KOMERCE_WEBHOOK_SECRET'),
        ],

    ],

];
