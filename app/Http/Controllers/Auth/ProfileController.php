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
    /**
     * Menampilkan form edit profil pengguna.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'info' => session('info'), // Untuk menampilkan pesan "wajib unggah foto"
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // 1. Ambil data yang sudah divalidasi oleh ProfileUpdateRequest
        $validatedData = $request->validated();
        
        // 2. Hapus kunci 'photo' dari array sebelum menggunakan fill().
        //    Ini memastikan kita hanya mengisi data teks (nama, email) dan mencegah error.
        unset($validatedData['photo']);
        
        $user->fill($validatedData);

        // Jika email diubah, reset status verifikasi email.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 3. Tangani unggahan file foto secara terpisah.
        if ($request->hasFile('photo')) {
            // Hapus foto lama dari storage jika ada.
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            // Simpan foto baru dan perbarui path di database.
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        // 4. "Segarkan" sesi pengguna dengan data baru.
        //    Ini penting agar middleware keamanan melihat status foto yang sudah diperbarui.
        $request->session()->flash('photo_just_uploaded', true);

        // 5. Arahkan pengguna ke dashboard dengan pesan sukses.
        return Redirect::route('dashboard')->with('success', 'Profil berhasil diperbarui dan akun Anda kini aktif!');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus juga foto profil dari storage saat akun dihapus.
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

