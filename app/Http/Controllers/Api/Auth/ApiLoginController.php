<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class ApiLoginController extends Controller
{
    /**
     * Menangani permintaan login dari API menggunakan token.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // 3. Verifikasi user dan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password yang Anda masukkan salah.'],
            ]);
        }

        // 4. Hapus token lama dan buat token baru (best practice)
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        // 5. Kirim respons sukses dengan token dan data user
        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil!',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * Menangani logout dari API dengan menghapus token.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil',
        ]);
    }
}
