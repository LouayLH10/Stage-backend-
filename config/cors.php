<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'], // autorise POST, GET, etc.
    'allowed_origins' => ['*'], // accepte toutes les origines
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // ⚠️ doit être false si '*' dans origins
];
