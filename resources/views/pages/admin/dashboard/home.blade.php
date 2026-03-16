<x-admin.layouts.dashboard title="Admin Dashboard" active="dashboard">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Admin Panel</p>
            <h1 class="mt-2 text-3xl font-black tracking-[0.08em] text-[#1e4fa3]">Operations Overview</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600">
                Monitor the student approval queue, see today&apos;s admin activity, and jump straight into the applications that need a decision.
            </p>
        </div>

        @if(isset($currentAdminUser) && $currentAdminUser)
            <div class="rounded-[1.4rem] border border-[#d5e0f0] bg-white/90 px-5 py-4 text-right shadow-[0_20px_50px_-35px_rgba(30,79,163,0.35)]">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Signed In As</p>
                <p class="mt-2 text-base font-bold text-[#1e4fa3]">
                    {{ trim(($currentAdminUser['first_name'] ?? '') . ' ' . ($currentAdminUser['last_name'] ?? '')) ?: 'Admin User' }}
                </p>
                <p class="mt-1 text-sm text-slate-500">{{ $currentAdminUser['email'] ?? '' }}</p>
            </div>
        @endif
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <section class="rounded-[1.7rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] p-6 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.45)]">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Total Students</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $totalStudents ?? 0 }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">All student accounts currently stored in the system.</p>
        </section>

        <section class="rounded-[1.7rem] border border-slate-200 bg-[linear-gradient(180deg,rgba(255,255,255,0.96),rgba(244,244,246,0.96))] p-6 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.45)]">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Pending Approvals</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $pendingApprovals ?? 0 }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Students who cannot enter the dashboard until an admin approves them.</p>
        </section>

        <section class="rounded-[1.7rem] border border-[#d7e2f5] bg-white/90 p-6 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.45)]">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Approved Today</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $approvedToday ?? 0 }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Students cleared by admin review today.</p>
        </section>

        <section class="rounded-[1.7rem] border border-[#d7e2f5] bg-white/90 p-6 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.45)]">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Active Now</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $activeNow ?? 0 }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Approved students currently marked as active in attendance tracking.</p>
        </section>
    </div>

    <section class="mt-8 rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#f1c74a] pb-4">
            <div>
                <h2 class="text-2xl font-bold text-[#1e4fa3]">System Alerts</h2>
                <p class="mt-1 text-sm text-slate-500">Priority signals based on the current approval queue.</p>
            </div>

            <a href="{{ route('admin.student-approvals') }}" wire:navigate
                class="inline-flex rounded-full border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] hover:bg-white">
                Open Approval Queue
            </a>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-3">
            @foreach(($alerts ?? []) as $alert)
                <article class="rounded-[1.4rem] border p-4 {{ ($alert['tone'] ?? 'blue') === 'emerald' ? 'border-emerald-200 bg-emerald-50' : ((($alert['tone'] ?? 'blue') === 'amber') ? 'border-amber-200 bg-amber-50' : ((($alert['tone'] ?? 'blue') === 'slate') ? 'border-slate-200 bg-slate-50' : 'border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))]')) }}">
                    <p class="text-sm font-bold {{ ($alert['tone'] ?? 'blue') === 'amber' ? 'text-amber-700' : ((($alert['tone'] ?? 'blue') === 'emerald') ? 'text-emerald-700' : ((($alert['tone'] ?? 'blue') === 'slate') ? 'text-slate-700' : 'text-[#1e4fa3]')) }}">
                        {{ $alert['title'] ?? 'Alert' }}
                    </p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $alert['description'] ?? '' }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.95fr)]">
        <section class="rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#f1c74a] pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#1e4fa3]">Recent Approval Requests</h2>
                    <p class="mt-1 text-sm text-slate-500">Newest students waiting for admin action.</p>
                </div>
                <a href="{{ route('admin.student-approvals') }}" wire:navigate class="text-sm font-semibold text-[#1e4fa3] hover:text-[#173d79]">Review all</a>
            </div>

            <div class="mt-5 space-y-3">
                @forelse(($recentRequests ?? []) as $request)
                    <article class="rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-base font-bold text-[#1e4fa3]">{{ $request['name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $request['email'] }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ ($request['profile_completed'] ?? false) ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-600' }}">
                                {{ ($request['profile_completed'] ?? false) ? 'Profile Complete' : 'Profile Incomplete' }}
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-500">
                            <p>Submitted {{ $request['submitted_at'] ?? 'Unknown' }}</p>
                            <a href="{{ route('admin.student-approvals') }}" wire:navigate class="font-semibold text-[#1e4fa3] hover:text-[#173d79]">Open in queue</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-12 text-center text-sm text-slate-500">
                        No pending approval requests right now.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
            <div class="border-b border-[#f1c74a] pb-4">
                <h2 class="text-2xl font-bold text-[#1e4fa3]">Latest Decisions</h2>
                <p class="mt-1 text-sm text-slate-500">Most recent approval and rejection outcomes.</p>
            </div>

            <div class="mt-5 space-y-3">
                @forelse(($recentDecisions ?? []) as $decision)
                    <article class="rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] px-4 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-base font-bold text-[#1e4fa3]">{{ $decision['name'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $decision['email'] }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] {{ ($decision['status'] ?? '') === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ ucfirst($decision['status'] ?? 'pending') }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-slate-500">Updated {{ $decision['decision_at'] ?? 'Unknown' }}</p>
                        @if(!empty($decision['note']))
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $decision['note'] }}</p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-12 text-center text-sm text-slate-500">
                        No approval decisions have been recorded yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-admin.layouts.dashboard>