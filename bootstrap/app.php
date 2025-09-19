<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:sanctum']]
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Mendaftarkan alias middleware untuk seluruh aplikasi.
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            // --- ALIAS BARU UNTUK FITUR WAJIB UPLOAD FOTO ---
            'profile.photo' => \App\Http\Middleware\EnsureProfilePhotoIsUploaded::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // --- MIDDLEWARE BARU DITERAPKAN DI SINI ---
        // Menambahkan middleware ke grup 'web' agar berjalan pada setiap request web
        // setelah sesi dimulai, memastikan pengguna siswa selalu dicek fotonya.
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EnsureProfilePhotoIsUploaded::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();