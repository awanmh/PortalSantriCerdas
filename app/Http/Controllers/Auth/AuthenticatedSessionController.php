<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login (SPA Inertia).
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Proses login user.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // 🔑 Kalau request dari SPA (axios/fetch), balikin JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Login berhasil',
                'user'    => $request->user(),
            ]);
        }

        // 🔑 Kalau request biasa (form), redirect ke dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Logout user.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 🔑 Balikin JSON kalau logout dari SPA
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['message' => 'Logout berhasil']);
        }

        return redirect('/');
    }
}
