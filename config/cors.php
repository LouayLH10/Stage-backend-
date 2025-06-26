<?php
return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
    ],

    'allowed_methods' => ['*'],

    // IMPORTANT : ne pas mettre '*' ici si you use credentials
    'allowed_origins' => [
        'http://localhost:3000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    // ✅ Permet d’envoyer les cookies avec les requêtes frontend
    'supports_credentials' => true,

];
