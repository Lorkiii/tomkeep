<x-admin.layouts.dashboard title="Site Management" active="sites">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Admin Panel</p>
            <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">Site Management</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600">
                Configure internship sites, maintain geofence radius values, and keep inactive locations available for historical review.
            </p>
        </div>

        <a href="{{ route('admin.sites.create') }}" class="inline-flex items-center justify-center rounded-full bg-[#1e4fa3] px-5 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79]">
            Add Site
        </a>
    </div>

    @if(session('admin_notice'))
        <div class="mb-6 rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('admin_notice') }}
        </div>
    @endif

    {{-- The Livewire table owns searching, status filters, and pagination so the page shell stays thin. --}}
    <livewire:admin.site-management-table />
</x-admin.layouts.dashboard>