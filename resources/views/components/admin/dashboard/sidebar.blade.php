@props(['active' => 'dashboard', 'currentAdminUser' => null])

@php
    // Build the admin's display name from the available profile fields.
    $adminName = trim(collect([
        $currentAdminUser['first_name'] ?? '',
        $currentAdminUser['middle_name'] ?? '',
        $currentAdminUser['last_name'] ?? '',
    ])->filter()->implode(' '));

    // Use initials as a simple visual identity in the sidebar.
    $initials = collect(explode(' ', $adminName ?: 'Admin User'))
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<aside class="flex h-full flex-col overflow-hidden rounded-[2rem] border border-white/60 bg-[linear-gradient(180deg,#1f4f9c_0%,#173d79_100%)] text-white shadow-[0_30px_80px_-40px_rgba(15,23,42,0.75)]">
    {{-- Admin brand/header area. --}}
    <div class="flex items-center justify-between gap-3 border-b border-white/10 px-5 py-5 lg:px-6">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/30 bg-white/10 text-sm font-extrabold tracking-[0.24em] text-white">
                AD
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/70">Administration</p>
                <p class="truncate text-lg font-bold tracking-[0.12em] text-white">TIMEKEEP</p>
            </div>
        </div>

        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20 lg:hidden"
            x-on:click="sidebarOpen = false"
            aria-label="Close admin sidebar"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    {{-- Admin profile card, nav items, and logout action. --}}
    <div class="flex flex-1 flex-col gap-6 overflow-y-auto px-4 py-6 lg:px-5">
        <section class="rounded-[1.6rem] border border-white/15 bg-white/8 p-4 backdrop-blur-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-white/40 bg-white/12 text-lg font-bold text-white shadow-inner shadow-white/10">
                    {{ $initials ?: 'AU' }}
                </div>

                <div class="min-w-0">
                    <p class="truncate text-base font-semibold text-white">{{ $adminName ?: 'Admin User' }}</p>
                    <p class="mt-1 truncate text-sm text-white/75">{{ $currentAdminUser['email'] ?? 'No email available' }}</p>
                </div>
            </div>
        </section>

        {{-- Admin navigation is intentionally small now, ready for future admin sections. --}}
        <nav class="flex-1 space-y-2">
            @php
                $items = [
                    ['key' => 'dashboard', 'label' => 'Admin Overview', 'route' => route('admin.dashboard')],
                    ['key' => 'approvals', 'label' => 'Student Approvals', 'route' => route('admin.student-approvals')],
                    ['key' => 'users', 'label' => 'User Management', 'route' => route('admin.users.index')],
                ];
            @endphp

            @foreach($items as $item)
                {{-- Highlight the active admin destination using the current page key. --}}
                <a
                    href="{{ $item['route'] }}"
                    class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition duration-200 {{ $active === $item['key'] ? 'bg-[#f2c84a] text-[#173d79] shadow-[0_18px_38px_-26px_rgba(242,200,74,0.95)]' : 'text-white/82 hover:bg-white/10 hover:text-white' }}"
                >
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $active === $item['key'] ? 'bg-[#173d79]' : 'bg-white/45' }}"></span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Shared logout button so admins leave through the same route as students. --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center rounded-2xl border border-white/15 bg-white/12 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20"
            >
                <span>Log Out</span>
            </button>
        </form>
    </div>
</aside>