<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://127.0.0.1:5173',
        'http://localhost:5173',
        'http://127.0.0.1:8000',
        'https://6fb5f343ce6a.ngrok-free.app',
    ],

    'allowed_origins_patterns' => [
        '^https:\/\/[a-z0-9]+\.ngrok-free\.app$',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
