<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'info' => session('info'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // --- Bagian 1: Menangani Unggahan Foto Profil ---
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada.
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Simpan foto baru dan dapatkan path relatifnya.
            $path = $request->file('photo')->store('profile-photos', 'public');
            
            // Simpan path ke database.
            $user->forceFill(['profile_photo_path' => $path])->save();

            // --- PERBAIKAN DEBUG DI SINI ---
            // Buat array berisi informasi debug untuk dikirim ke frontend.
            $debugInfo = [
                'pesan' => 'Foto berhasil disimpan di server.',
                'path_relatif' => $path,
                'path_fisik' => Storage::disk('public')->path($path),
                'url_dihasilkan' => Storage::disk('public')->url($path),
            ];

            // Arahkan kembali dengan pesan sukses DAN pesan debug.
            return Redirect::route('profile.edit')
                ->with('success', 'Foto profil berhasil diperbarui.')
                ->with('debug_photo', $debugInfo); // <-- Mengirim debug info
        }

        // --- Bagian 2: Menangani Pembaruan Data Teks ---
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Informasi profil berhasil diperbarui.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
}

