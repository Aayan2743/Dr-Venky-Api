<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // Allow all HTTP methods (GET, POST, PUT, DELETE, etc.)

    'allowed_origins' => ['*'], // Replace '*' with the specific domain, e.g., 'https://example.com'

    // 'allowed_origins_patterns' => [],
    'allowed_origins_patterns' => ['*'],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Enable if using cookies or authorization headers
];

