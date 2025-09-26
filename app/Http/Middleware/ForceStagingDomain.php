<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceStagingDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Trik ini memastikan Laravel mempercayai domain dan port frontend
        // untuk otentikasi sesi, mengatasi masalah header Referer.
        if (config('app.env') === 'local') {
            $request->headers->set('host', parse_url(config('app.url'), PHP_URL_HOST));
        }

        return $next($request);
    }
}
```

**Langkah 3: Daftarkan Middleware Baru**

Sekarang, kita perlu memberitahu Laravel untuk menggunakan middleware ini. Buka file `app/Http/Kernel.php`.

Di dalam *array* `$middlewareGroups`, tambahkan middleware baru kita **di atas** middleware Sanctum di dalam grup `'api'`.

```php
// app/Http/Kernel.php

protected $middlewareGroups = [
    'web' => [
        // ... middleware web lainnya
    ],

    'api' => [
        // TAMBAHKAN BARIS INI DI SINI
        \App\Http\Middleware\ForceStagingDomain::class,

        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
