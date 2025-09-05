<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // PASTIKAN BARIS INI ADA!
        // Ini memberitahu Laravel untuk tidak melakukan pengecekan CSRF
        // pada semua rute yang dimulai dengan /api/
        'api/*',
    ];
}
