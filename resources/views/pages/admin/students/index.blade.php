<x-admin.layouts.dashboard title="Student Profiles" active="students-profiles">
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Students</p>
            <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">Student Profiles</h1>
            <p class="mt-3 max-w-3xl text-sm text-slate-600">
                Browse all registered students, search by name or email, and open any record for review.
            </p>
        </div>

        @if(isset($currentAdminUser) && $currentAdminUser)
            <div class="w-full rounded-[1.4rem] border border-[#d5e0f0] bg-white/90 px-5 py-4 text-left shadow-[0_20px_50px_-35px_rgba(30,79,163,0.35)] sm:max-w-sm sm:text-right">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Viewing As</p>
                <p class="mt-2 text-base font-bold text-[#1e4fa3]">{{ trim(($currentAdminUser['first_name'] ?? '') . ' ' . ($currentAdminUser['last_name'] ?? '')) ?: 'Admin User' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $currentAdminUser['email'] ?? '' }}</p>
            </div>
        @endif
    </div>

    <livewire:admin.user-management-table role="student" />
</x-admin.layouts.dashboard>

