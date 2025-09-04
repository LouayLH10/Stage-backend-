<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // pour dev uniquement
    'allowed_headers' => ['*'],
    'supports_credentials' => false,
];
