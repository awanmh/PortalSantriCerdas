//resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Santri Cerdas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    {{-- Navbar --}}
    @include('components.navbar')

    <div class="flex">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Konten Utama --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    {{-- Footer --}}
    @include('components.footer')
</body>
</html>
