<?php

return [
    'base_url' => [
        'prod' => [
            'api' => 'https://api.salla.sa/admin/v2',
            'auth' => 'https://accounts.salla.sa/oauth2',
            'verify_ssl' => true,
        ],
        'dev' => [
            'api' => env('SALLA_API_URL', 'https://api.salla.dev/admin/v2'),
            'auth' => env('SALLA_AUTH_URL', 'https://accounts.salla.sa/oauth2'),
            'verify_ssl' => env('SALLA_VERIFY_SSL', false),
        ],
    ],
    'client_id' => env('SALLA_CLIENT_ID'),
    'client_secret' => env('SALLA_CLIENT_SECRET'),  
    'redirect_uri' => env('SALLA_REDIRECT_URI'),
    'access_token' => env('SALLA_ACCESS_TOKEN'), // إضافة الـ token مباشرة
    'environment' => 'dev', // استخدام dev environment لـ api.salla.dev
    'token_storage' => [
        'driver' => 'file',
        'file' => [
            'path' => storage_path('app/salla_token.json'),
        ],
        'database' => [
            'table' => 'salla_tokens',
        ],
    ],
    'scopes' => [
        'offline_access',
        'products',
        'orders',
    ],
    'timeout' => env('SALLA_TIMEOUT', 60),
    'connect_timeout' => env('SALLA_CONNECT_TIMEOUT', 15),
    'retry' => [
        'attempts' => env('SALLA_RETRY_ATTEMPTS', 3),
        'sleep' => env('SALLA_RETRY_SLEEP', 1000),
        'on_status' => [429, 500, 502, 503, 504],
    ],
];
