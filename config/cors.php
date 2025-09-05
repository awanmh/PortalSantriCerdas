<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
     * Menambahkan URL aplikasi utama (:8000) di samping URL Vite (:5173).
     * Ini adalah praktik terbaik untuk memastikan semua interaksi diizinkan.
     */
    'allowed_origins' => [
        'http://127.0.0.1:5173', // Untuk Vite Dev Server
        'http://127.0.0.1:8000', // Untuk Aplikasi Utama
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
     * WAJIB 'true' agar frontend diizinkan untuk mengirim
     * dan menerima cookie sesi untuk otentikasi.
     */
    'supports_credentials' => true,

];

