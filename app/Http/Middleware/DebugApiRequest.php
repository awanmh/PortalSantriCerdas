<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mencatat semua informasi penting ke dalam file log
        Log::info('--- [API DEBUG START] ---');
        Log::info('URL: ' . $request->fullUrl());
        Log::info('Method: ' . $request->method());
        Log::info('Has Session Middleware: ' . ($request->hasSession() ? 'Yes' : 'No'));
        Log::info('Session ID: ' . ($request->hasSession() ? $request->session()->getId() : 'N/A'));
        Log::info('User Authenticated at START: ' . (Auth::check() ? 'Yes - ID: ' . Auth::id() : 'No'));
        Log::info('--- [API DEBUG END] ---');

        // Lanjutkan request ke middleware berikutnya
        return $next($request);
    }
}
