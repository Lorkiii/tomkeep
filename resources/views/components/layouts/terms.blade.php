{{-- Terms page layout used during pre-login onboarding flow. --}}
{{-- Terms page: dark header/footer, light blue-to-white gradient (brightest at center), centered card --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms and Conditions - Web Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-800 text-gray-900 antialiased">
    <header class="bg-gray-800 px-4 py-2.5 text-sm font-medium text-white">
        Terms and Conditions - Web Page
    </header>
    <main class="flex min-h-[calc(100vh-6rem)] flex-col items-center justify-center p-6" style="background: radial-gradient(ellipse at center, #ffffff 0%, #f0f9ff 40%, #e0f2fe 100%);">
        <div class="w-full max-w-2xl">
            {{ $slot }}
        </div>
    </main>
    <footer class="flex items-center justify-center gap-1.5 bg-gray-800 px-4 py-2.5 text-center text-xs text-gray-400">
        <span>Copyright © {{ date('Y') }}. Powered by</span>
        <span class="inline-flex h-4 w-4 items-center justify-center text-gray-400">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
    </footer>

    @livewireScripts
</body>
</html>
