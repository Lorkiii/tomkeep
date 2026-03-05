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
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    {{-- Top bar: page title (full width) --}}
    <header class="border-b border-slate-200 bg-slate-800 px-4 py-2 text-sm font-medium text-white">
        {{ $title }} - Web
    </header>
    {{-- Sidebar: fixed on the left so it always shows --}}
    <aside class="fixed left-0 top-9 z-20 flex h-[calc(100vh-2.25rem)] w-64 flex-col border-r border-[#2f56b0] bg-[#2f56b0] text-white">
            <div class="flex flex-1 flex-col overflow-y-auto p-4">
                {{-- Profile: white circle, name, email --}}
                <div class="flex flex-col items-center pb-6">
                    <div class="h-20 w-20 rounded-full border-2 border-white bg-white/10"></div>
                    @if(isset($currentOjtUser) && $currentOjtUser)
                        <p class="mt-3 text-center text-sm font-medium text-white">
                            {{ trim(($currentOjtUser['first_name'] ?? '') . ' ' . ($currentOjtUser['middle_name'] ?? '') . ' ' . ($currentOjtUser['last_name'] ?? '')) ?: 'User' }}
                        </p>
                        <p class="mt-0.5 max-w-full truncate text-center text-xs text-white">
                            {{ $currentOjtUser['email'] ?? '' }}
                        </p>
                    @endif
                </div>
                {{-- Nav: thin light gray lines between items, active = light yellow/gold --}}
                <nav class="flex-1">
                    <a href="{{ route('home') }}" class="block border-b border-gray-400/40 px-3 py-3 text-sm font-medium text-white transition {{ $active === 'dashboard' ? 'bg-amber-400/90 text-slate-900' : 'hover:bg-white/10' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('account.settings') }}" class="block border-b border-gray-400/40 px-3 py-3 text-sm font-medium text-white transition {{ $active === 'account' ? 'bg-amber-400/90 text-slate-900' : 'hover:bg-white/10' }}">
                        Account Settings
                    </a>
                    <a href="{{ route('monthly.dtr') }}" class="block border-b border-gray-400/40 px-3 py-3 text-sm font-medium text-white transition {{ $active === 'dtr' ? 'bg-amber-400/90 text-slate-900' : 'hover:bg-white/10' }}">
                        My Monthly DTR
                    </a>
                    <a href="{{ route('terms.dashboard') }}" class="block border-b border-gray-400/40 px-3 py-3 text-sm font-medium text-white transition {{ $active === 'terms' ? 'bg-amber-400/90 text-slate-900' : 'hover:bg-white/10' }}">
                        Terms and Conditions
                    </a>
                </nav>
                {{-- Log Out: light blue #5e85c8, rounded, centered --}}
                <form action="{{ route('logout') }}" method="POST" class="pt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-[#5e85c8] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#4d74b8]">
                        Log Out
                    </button>
                </form>
            </div>
    </aside>

    {{-- Main content: margin-left so it sits to the right of the sidebar --}}
    <main class="min-h-[calc(100vh-2.25rem)] flex-1 overflow-auto p-6 pl-[17rem]">
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
