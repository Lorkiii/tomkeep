{{-- Terms page: white-to-light-blue gradient, centered horizontal card, smooth UI --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms and Conditions - Web Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen text-gray-900 antialiased" style="background: linear-gradient(to bottom, #FFFFFF 0%, #F8FAFC 25%, #EEF3F8 100%);">
    <main class="relative flex min-h-screen flex-col items-center justify-center p-6">
        <div class="w-full max-w-4xl">
            {{ $slot }}
        </div>
        <footer class="absolute bottom-4 right-4 flex items-center gap-1.5 text-xs text-gray-500">
            <span>Copyright © {{ date('Y') }} Powered by</span>
            <span class="inline-flex h-4 w-4 items-center justify-center"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></span>
        </footer>
    </main>

    @livewireScripts
    <script>
        try { localStorage.removeItem('ojt_current_user'); } catch (e) {}
    </script>
</body>
</html>
