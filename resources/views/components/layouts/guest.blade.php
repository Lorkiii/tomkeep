<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'OJT LOGS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#e8ebf0] text-slate-900 antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            {{ $slot }}
        </div>
    </main>
    @livewireScripts
    <script>
        try { localStorage.removeItem('ojt_current_user'); } catch (e) {}
    </script>
</body>
</html>
