<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- META UTAMA -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token - PERBAIKAN -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Tambahkan ini untuk Inertia -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        
        <!-- Tambahkan script CSRF untuk Axios -->
        <script>
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>