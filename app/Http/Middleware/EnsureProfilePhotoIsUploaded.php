<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfilePhotoIsUploaded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // --- UNTUK SEMENTARA, LOGIKA INI DINONAKTIFKAN ---
        // Dengan mengomentari blok 'if' ini, siswa tidak akan lagi dipaksa
        // untuk pergi ke halaman profil jika mereka belum mengunggah foto.
        /*
        if (
            $user &&
            $user->hasRole('siswa') &&
            is_null($user->profile_photo_path) &&
            !$request->routeIs('profile.edit') &&
            !$request->routeIs('logout')
        ) {
            // Arahkan paksa ke halaman edit profil dengan pesan
            return redirect()->route('profile.edit')
                ->with('info', 'Anda harus mengunggah foto profil terlebih dahulu untuk melanjutkan.');
        }
        */

        return $next($request);
    }
}

