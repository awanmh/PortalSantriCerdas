<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
    <head>
        <!-- META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Portal Santri Cerdas - Aplikasi monitoring dan pembelajaran santri berbasis web">
        <meta name="author" content="SMK Al-Ikhlash">

        <!-- Title -->
        <title inertia>{{ config('app.name', 'Portal Santri Cerdas') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <!-- Font Islami (judul/logo) -->
        <link href="https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
        <!-- Font modern untuk body -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Inertia -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <!-- Setup global CSRF untuk JS/Axios -->
        <script>
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
        </script>
    </head>
    <body class="h-full font-sans bg-gray-50 text-gray-900 dark:bg-slate-900 dark:text-gray-100">
        @inertia
    </body>
</html>
