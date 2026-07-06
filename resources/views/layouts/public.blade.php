<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jadwal Pelajaran') — {{ config('app.name') }}</title>
    {{-- Tailwind via CDN untuk halaman publik (singleton, tidak butuh build). --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>
    <footer class="mx-auto max-w-6xl px-4 pb-8 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>
</html>