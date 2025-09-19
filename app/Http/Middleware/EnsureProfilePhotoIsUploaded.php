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
        // --- PERBAIKAN DI SINI ---
        // Jika sesi memiliki "tanda" bahwa foto baru saja diunggah,
        // lewati semua pemeriksaan dan langsung lanjutkan ke permintaan berikutnya (dashboard).
        // Tanda ini hanya berlaku untuk satu permintaan dan akan otomatis hilang setelahnya.
        if ($request->session()->has('photo_just_uploaded')) {
            return $next($request);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Cek jika user adalah siswa, belum punya foto, dan tidak sedang di halaman profil/logout
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

        return $next($request);
    }
}

