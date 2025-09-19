<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- META -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Portal Santri Cerdas') }}</title>

        <!-- Fonts -->
        <!-- Font Islami (judul/logo) -->
        <link href="https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
        <!-- Font modern untuk body -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Scripts -->
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
    <body class="font-sans antialiased bg-gray-50 dark:bg-slate-900">
        @inertia
    </body>
</html>
