{{-- Student dashboard shell with sidebar navigation and authenticated user context. --}}
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

<body class="min-h-screen bg-[#f3f5f9] font-sans text-slate-900 antialiased">
    {{-- Alpine keeps track of mobile open state and desktop collapsed state for the sidebar. --}}
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
        class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,rgba(214,227,255,0.95),rgba(243,245,249,0.98)_42%,rgba(243,245,249,1)_100%)]">
        {{-- Decorative background layer so the dashboard does not feel flat. --}}
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[linear-gradient(180deg,rgba(30,79,163,0.08),rgba(30,79,163,0))]">
        </div>

        {{-- Desktop shell: sidebar on the left, page content on the right. --}}
        <div class="relative mx-auto flex min-h-screen w-full max-w-[1920px]">
            {{-- Desktop sidebar stays visible and can collapse to a narrower width. --}}
            <div class="hidden shrink-0 transition-all duration-300 lg:block"
                x-bind:class="sidebarCollapsed ? 'w-28 p-4 pr-0' : 'w-[20rem] p-4 pr-0'">
                <div class="sticky top-0 h-[calc(100vh-2rem)] py-1">
                    <x-student.dashboard.sidebar :active="$active" :current-ojt-user="$currentOjtUser ?? null" />
                </div>
            </div>

            {{-- Mobile overlay appears behind the slide-in sidebar drawer. --}}
            <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-30 bg-slate-950/45 backdrop-blur-[2px] lg:hidden"
                x-transition.opacity x-on:click="sidebarOpen = false"></div>

            {{-- Mobile sidebar drawer uses the same sidebar component as desktop. --}}
            <div class="fixed inset-y-0 left-0 z-40 w-[18.5rem] p-4 pr-0 transition duration-300 lg:hidden" x-cloak
                x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <x-student.dashboard.sidebar :active="$active" :current-ojt-user="$currentOjtUser ?? null" />
            </div>

            {{-- Main content column where each page injects its own markup through the slot. --}}
            <main class="relative flex-1 p-4 pt-20 sm:p-6 sm:pt-24 lg:p-8 lg:pt-8 xl:px-10 2xl:px-12">
                {{-- Hamburger button only appears on mobile because desktop already shows the sidebar. --}}
                <button type="button"
                    class="fixed left-4 top-4 z-20 inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/70 bg-white/90 text-[#1e4fa3] shadow-[0_18px_40px_-24px_rgba(15,23,42,0.7)] backdrop-blur lg:hidden"
                    x-on:click="sidebarOpen = true" aria-label="Open sidebar">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>

                {{-- This slot is the page body coming from home, account settings, monthly DTR, or terms. --}}
                <div class="mx-auto w-full max-w-[1480px]">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>