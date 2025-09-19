<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            // Bagikan data otentikasi secara global
            'auth' => function () use ($request) {
                if (! $request->user()) {
                    return null;
                }

                // --- LOGIKA PERIZINAN DIPERBARUI ---
                $permissions = $request->user()->hasRole('it')
                    // Jika user adalah 'it', berikan semua izin yang ada di sistem.
                    ? Permission::pluck('name')
                    // Jika bukan, berikan izin spesifik yang dimilikinya.
                    : $request->user()->getAllPermissions()->pluck('name');

                return [
                    'user' => [
                        'id'          => $request->user()->id,
                        'name'        => $request->user()->name,
                        'email'       => $request->user()->email,
                        'roles'       => $request->user()->getRoleNames(),
                        'permissions' => $permissions, // Menggunakan variabel yang sudah diproses
                    ],
                ];
            },

            // Bagikan rute Ziggy agar helper `route()` berfungsi di Vue
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],

            // Bagikan flash messages dari session untuk notifikasi
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
            ],
        ];
    }
}