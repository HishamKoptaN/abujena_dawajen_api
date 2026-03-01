<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'serve' => true,
            'throw' => false,
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'visibility' => 'private',
            'options' => [
                'CacheControl' => 'max-age=31536000',
                'Metadata' => [
                    'uploaded-by' => 'Laravel',
                ],
            ],
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
    'custom_base_url' => env('FILES_BASE_URL', 'https://api.aquan.website'),
];
