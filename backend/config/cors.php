<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        // Your production Vercel domain:
        'https://world-cup-hub-w2od-5dcemymsz-md-nafiz-al-ifats-projects.vercel.app',
    ],
    // Allow preview deploys too:
    'allowed_origins_patterns' => ['#^https://.*\.vercel\.app$#'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
