<x-admin.layouts.dashboard title="Attendance Policy" active="attendance-settings">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">System Settings</p>
            <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">Attendance Policy</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600">
                Set one global work-from-home distance limit for all students. New WFH time-ins will save the current rule on each DTR so historical records stay stable even after later policy changes.
            </p>
        </div>

        <a href="{{ route('admin.attendance.reports') }}" class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-5 py-3 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">
            Back to Attendance Reports
        </a>
    </div>

    @if(session('admin_notice'))
        <div class="mb-6 rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('admin_notice') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-[1.4rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            <p class="font-semibold">Please review the attendance policy value before saving.</p>
        </div>
    @endif

    <form action="{{ route('admin.settings.attendance.update') }}" method="POST" class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        @csrf
        @method('PATCH')

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.85fr)]">
            <section>
                <div class="border-b border-[#f1c74a] pb-4">
                    <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">WFH Anchor Limit</h2>
                    <p class="mt-1 text-sm text-slate-500">This limit is used when a student times out from a work-from-home day. If the student is too far from the original time-in location, the time-out is rejected.</p>
                </div>

                <div class="mt-5 space-y-4">
                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Allowed Movement From Time-In Point (Meters)</span>
                        <input
                            name="wfh_anchor_limit_m"
                            type="number"
                            min="1"
                            max="5000"
                            value="{{ old('wfh_anchor_limit_m', $wfhAnchorLimit) }}"
                            class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white"
                        >
                        @error('wfh_anchor_limit_m') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </label>

                    <div class="rounded-[1.4rem] border border-[#d7e2f5] bg-[#f7f9fc] px-4 py-4 text-sm text-slate-600">
                        <p class="font-semibold text-[#1e4fa3]">How this rule works</p>
                        <p class="mt-2">This is one global admin setting for everyone.</p>
                        <p class="mt-2">The saved value is copied into each new WFH daily time record so older records keep the rule that was active when they started.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] px-5 py-5 text-sm text-slate-600">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Policy Notes</p>
                <ul class="mt-4 space-y-3">
                    <li>Use a smaller number for stricter WFH anchoring.</li>
                    <li>Use a larger number if normal location drift should still be accepted.</li>
                    <li>This only affects WFH timeout validation, not on-site geofence classification.</li>
                </ul>
            </section>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#1e4fa3] px-5 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79]">
                Save Attendance Policy
            </button>
        </div>
    </form>
</x-admin.layouts.dashboard>