<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET'],
    'allowed_origins' => array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'http://localhost,http://localhost:3000,http://127.0.0.1')))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Accept', 'Content-Type'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => false,
];
