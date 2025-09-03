<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $request->authenticate();

        // HANYA gunakan session untuk permintaan web
        if (!$request->wantsJson()) {
            $request->session()->regenerate();
        }

        // Jika permintaan ingin respons JSON (API request)
        if ($request->wantsJson()) {
            return response()->json([
                'token' => $request->user()->createToken('api-token')->plainTextToken
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        Auth::guard('web')->logout();

        // HANYA gunakan session untuk permintaan web
        if (!$request->wantsJson()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Jika permintaan ingin respons JSON (API request)
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }

        return redirect('/');
    }
}
