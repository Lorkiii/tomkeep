{{-- Guest layout used by splash/auth onboarding screens. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'OJT LOGS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen text-slate-900 antialiased" style="background: linear-gradient(to bottom, #e0f2fe 0%, #f0f9ff 30%, #ffffff 100%);">
    <main class="flex min-h-screen flex-col items-center justify-center p-4">
        <div class="w-full max-w-4xl px-4">
            {{ $slot }}
        </div>
    </main>
    @livewireScripts
</body>
</html>
