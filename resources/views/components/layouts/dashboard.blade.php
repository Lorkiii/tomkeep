@props(['title' => 'Dashboard', 'active' => 'dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Web</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#dfe4f1] text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-slate-800 px-4 py-2 text-sm font-medium text-white">
        {{ $title }} - Web
    </header>
    <x-sidebar :active="$active" :user="$currentOjtUser ?? null">
        <x-slot:footer>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-[#5e85c8] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#4d74b8]">
                    Log Out
                </button>
            </form>
        </x-slot:footer>
    </x-sidebar>

    <main class="min-h-[calc(100vh-2.25rem)] flex-1 overflow-auto px-10 py-8 pl-[18rem]">
        {{ $slot }}
    </main>

    @livewireScripts
    @isset($currentOjtUser)
        <script>
            (function() {
                var user = @json(collect($currentOjtUser ?? [])->except('password')->all());
                try {
                    if (user && Object.keys(user).length) {
                        localStorage.setItem('ojt_current_user', JSON.stringify(user));
                    }
                } catch (e) {}
            })();
        </script>
    @endisset
</body>
</html>
