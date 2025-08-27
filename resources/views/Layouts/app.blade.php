<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Santri Cerdas')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-santriBg font-sans text-gray-800">
    <x-navbar />

    <div class="flex min-h-screen">
        <x-sidebar />

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <x-footer />
</body>
</html>
