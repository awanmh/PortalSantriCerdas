<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistem Informasi Pesantren</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Laila:wght@700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">


    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="antialiased">
    <div class="container">
        <header>
            <h1>Sistem Informasi Akademik Santri</h1>
            <p>Selamat Datang di Portal Digital Pesantren Modern Kami</p>
        </header>

        <main>
            <div class="card-login">
                <h2>Silakan Masuk</h2>
                <a href="/login" class="btn">Login</a>
                <a href="/register" class="btn btn-secondary">Daftar</a>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Pesantren Digital. Hak Cipta Dilindungi.</p>
        </footer>
    </div>
</body>
</html>