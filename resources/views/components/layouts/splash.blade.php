{{-- Splash: light blue gradient, full viewport, footer. Used by SplashScreen Livewire component. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms and Conditions - Web Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-white text-slate-900 antialiased">
    <main class="min-h-screen">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
