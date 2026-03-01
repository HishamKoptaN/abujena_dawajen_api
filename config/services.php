<?php

return [
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'firebase' => [
        'credentials' => base_path(env('FIREBASE_CREDENTIALS_PATH')),
    ],
    'salla' => [
        'dev_url' => env('SALLA_API_DEV_URL', 'https://api.salla.dev/admin/v2'),
        'prod_url' => env('SALLA_API_PROD_URL', 'https://api.salla.sa/admin/v2'),
    ],
];
