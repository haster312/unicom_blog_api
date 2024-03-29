<?php

return [
    'APP_NAME' => 'My UniCom',
    'HASH_KEY' => env('HASH', '123456789'),
    'S3' => [
        'REGION' => env('S3_REGION', 'us-east-1'),
        'KEY' => env('S3_KEY', ''),
        'SECRET' => env('S3_SECRET', ''),
        'BUCKET' => env('S3_BUCKET', ''),
    ]
];
