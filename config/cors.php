<?php
return [
    'paths' => ['api/*'],
    'allowed_origins' => ['http://localhost:3000'], // URL de votre front
    'allowed_methods' => ['*'],
    'allowed_headers' => [       '*',
        'Content-Type', // ← Essentiel pour FormData
        'X-Requested-With',
        'Authorization'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
