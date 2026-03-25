@props(['title' => 'Admin Dashboard', 'active' => 'dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-[#f3f5f9] font-sans text-slate-900 antialiased">
    {{-- Admin layout keeps the same palette as the student dashboard for a consistent system-wide shell. --}}
    <div x-data="{ sidebarOpen: false }" class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,rgba(214,227,255,0.95),rgba(243,245,249,0.98)_42%,rgba(243,245,249,1)_100%)]">
        {{-- Shared top glow matches the student dashboard atmosphere. --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[linear-gradient(180deg,rgba(30,79,163,0.08),rgba(30,79,163,0))]"></div>

        {{-- Shared admin shell: sidebar plus content area. --}}
        <div class="relative mx-auto flex min-h-screen w-full max-w-[1920px] lg:h-screen lg:overflow-hidden">
            {{-- Desktop admin navigation column. --}}
            <div class="hidden w-[19rem] shrink-0 p-4 pr-0 lg:block">
                <div class="h-[calc(100vh-2rem)] py-1">
                    <x-admin.dashboard.sidebar :active="$active" :current-admin-user="$currentAdminUser ?? null" />
                </div>
            </div>

            {{-- Mobile overlay closes the drawer when clicked. --}}
            <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-30 bg-slate-950/45 backdrop-blur-[2px] lg:hidden"
                x-transition.opacity x-on:click="sidebarOpen = false"></div>

            {{-- Mobile admin drawer reuses the same sidebar component as desktop. --}}
            <div class="fixed inset-y-0 left-0 z-40 w-[18.5rem] p-4 pr-0 transition duration-300 lg:hidden" x-cloak
                x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <x-admin.dashboard.sidebar :active="$active" :current-admin-user="$currentAdminUser ?? null" />
            </div>

            {{-- Main admin page content gets injected through the component slot. --}}
            <main class="relative flex-1 p-4 pt-20 sm:p-6 sm:pt-24 lg:h-screen lg:overflow-y-auto lg:p-8 lg:pt-8 xl:px-10 2xl:px-12">
                <button type="button"
                    class="fixed left-4 top-4 z-20 inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/70 bg-white/90 text-[#1e4fa3] shadow-[0_18px_40px_-24px_rgba(15,23,42,0.7)] backdrop-blur lg:hidden"
                    x-on:click="sidebarOpen = true" aria-label="Open admin sidebar">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>

                {{-- The current admin page is rendered here. --}}
                <div class="mx-auto w-full max-w-[1480px]">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>