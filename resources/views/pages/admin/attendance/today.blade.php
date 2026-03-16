<x-admin.layouts.dashboard title="Today's Attendance" active="attendance-today">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Attendance Monitoring</p>
            <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">Today&apos;s Attendance View</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600">
                Observe who already started, who has completed the full sequence, and how many work-hours were logged today.
            </p>
        </div>

        <a href="{{ route('admin.attendance.reports') }}" wire:navigate class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-5 py-3 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">
            Open Reports
        </a>
    </div>

    <livewire:admin.today-attendance-table />
</x-admin.layouts.dashboard>