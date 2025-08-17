<?php

return [
    'paths' => ['sanctum/csrf-cookie', 'api/*', 'register', 'login', 'logout'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:4000'], // 👈 Your React app URL
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // 🔥 Required for cookies
];