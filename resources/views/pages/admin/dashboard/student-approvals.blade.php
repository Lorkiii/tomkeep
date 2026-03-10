<x-admin.layouts.dashboard title="Student Approvals" active="approvals">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Admin Panel</p>
            <h1 class="mt-2 text-3xl font-black tracking-[0.08em] text-[#1e4fa3]">Student Approvals</h1>
            <p class="mt-3 max-w-3xl text-sm text-slate-600">
                Review new student registrations, confirm that the profile is complete, and approve or reject access to the student dashboard.
            </p>
        </div>

        @if(isset($currentAdminUser) && $currentAdminUser)
            <div class="rounded-[1.4rem] border border-[#d5e0f0] bg-white/90 px-5 py-4 text-right shadow-[0_20px_50px_-35px_rgba(30,79,163,0.35)]">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Reviewing As</p>
                <p class="mt-2 text-base font-bold text-[#1e4fa3]">
                    {{ trim(($currentAdminUser['first_name'] ?? '') . ' ' . ($currentAdminUser['last_name'] ?? '')) ?: 'Admin User' }}
                </p>
                <p class="mt-1 text-sm text-slate-500">{{ $currentAdminUser['email'] ?? '' }}</p>
            </div>
        @endif
    </div>

    <livewire:admin.student-approval-table />
</x-admin.layouts.dashboard>