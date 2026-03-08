@props(['active' => 'dashboard', 'currentOjtUser' => null])

@php
    // Build a readable full name for the profile card.
    $userName = trim(collect([
        $currentOjtUser['first_name'] ?? '',
        $currentOjtUser['middle_name'] ?? '',
        $currentOjtUser['last_name'] ?? '',
    ])->filter()->implode(' '));

    // Use initials as a lightweight avatar fallback.
    $initials = collect(explode(' ', $userName ?: 'OJT User'))
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

{{--
    Reusable dashboard sidebar.

    Why extract this from the layout?
    - The layout becomes easier to read.
    - Desktop and mobile can share the same navigation markup.
    - Future changes to nav links only happen in one file.
--}}
<aside class="flex h-full flex-col overflow-hidden rounded-[2rem] border border-white/60 bg-[linear-gradient(180deg,#1f4f9c_0%,#173d79_100%)] text-white shadow-[0_30px_80px_-40px_rgba(15,23,42,0.75)]">
    {{-- Top brand row with close/collapse controls. --}}
    <div class="flex items-center justify-between gap-3 border-b border-white/10 px-5 py-5 lg:px-6">
        <div class="flex items-center gap-3 overflow-hidden">
            {{-- Small brand mark. --}}
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/30 bg-white/10 text-sm font-extrabold tracking-[0.28em] text-white">
                OJ
            </div>

            {{-- Text label is hidden when the desktop sidebar is collapsed. --}}
            <div x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/70">Attendance</p>
                <p class="truncate text-lg font-bold tracking-[0.18em] text-white">OJT LOGS</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Mobile close button for the slide-in drawer. --}}
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20 lg:hidden"
                x-on:click="sidebarOpen = false"
                aria-label="Close sidebar"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>

            {{-- Desktop collapse button. --}}
            <button
                type="button"
                class="hidden h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20 lg:inline-flex"
                x-on:click="sidebarCollapsed = !sidebarCollapsed"
                aria-label="Toggle sidebar"
            >
                <svg class="h-5 w-5 transition-transform duration-200" x-bind:class="sidebarCollapsed ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Scrollable body keeps navigation usable on short screens. --}}
    <div class="flex flex-1 flex-col gap-6 overflow-y-auto px-4 py-6 lg:px-5">
        {{-- Profile summary card. --}}
        <section class="rounded-[1.6rem] border border-white/15 bg-white/8 p-4 backdrop-blur-sm">
            <div class="flex items-center gap-4" x-bind:class="sidebarCollapsed ? 'justify-center' : ''">
                {{-- Initial-based avatar. --}}
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-white/40 bg-white/12 text-lg font-bold text-white shadow-inner shadow-white/10">
                    {{ $initials ?: 'OU' }}
                </div>

                {{-- User details disappear in collapsed mode to save width. --}}
                <div x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="min-w-0">
                    <p class="truncate text-base font-semibold text-white">{{ $userName ?: 'OJT User' }}</p>
                    <p class="mt-1 truncate text-sm text-white/75">{{ $currentOjtUser['email'] ?? 'No email available' }}</p>
                </div>
            </div>
        </section>

        {{-- Navigation links are defined in a local array for easy maintenance. --}}
        <nav class="flex-1 space-y-2">
            @php
                $items = [
                    ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => route('home')],
                    ['key' => 'account', 'label' => 'Account Settings', 'route' => route('account.settings')],
                    ['key' => 'dtr', 'label' => 'My Monthly DTR', 'route' => route('monthly.dtr')],
                    ['key' => 'terms', 'label' => 'Terms and Conditions', 'route' => route('terms.dashboard')],
                ];
            @endphp

            @foreach($items as $item)
                {{--
                    Active item gets the gold highlight.
                    In collapsed mode we keep only the bullet marker visible.
                --}}
                <a
                    href="{{ $item['route'] }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition duration-200 {{ $active === $item['key'] ? 'bg-[#f2c84a] text-[#173d79] shadow-[0_18px_38px_-26px_rgba(242,200,74,0.95)]' : 'text-white/82 hover:bg-white/10 hover:text-white' }}"
                    x-bind:class="sidebarCollapsed ? 'justify-center px-3' : ''"
                >
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $active === $item['key'] ? 'bg-[#173d79]' : 'bg-white/45' }}"></span>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Logout stays at the bottom of the sidebar. --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center rounded-2xl border border-white/15 bg-white/12 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20"
                x-bind:class="sidebarCollapsed ? 'px-3' : ''"
            >
                <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>Log Out</span>
                <span x-show="sidebarCollapsed" x-transition.opacity.duration.200ms aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 16l4-4m0 0l-4-4m4 4H9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 20H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7" />
                    </svg>
                </span>
            </button>
        </form>
    </div>
</aside>