<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Login user (guru, siswa, bk, it)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = Auth::user();

        // Buat token API (jika SPA/API)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->roles->first()->name ?? null,
            ],
            'token' => $token
        ]);
    }

    /**
     * Logout user dan hapus token
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Hapus semua token API user ini
            $user->tokens()->delete();
        }

        Auth::logout();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Ambil data user yang login
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->roles->first()->name ?? null,
            ]
        ]);
    }
}
