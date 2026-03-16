<x-admin.layouts.dashboard title="Edit Site" active="sites">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Site Management</p>
            <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">{{ $managedSite->company_name }}</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600">
                Update the site address, tune its geofence radius, and control whether the location is still available for new attendance activity.
            </p>
        </div>

        <div class="rounded-[1.4rem] border border-[#d5e0f0] bg-white/90 px-5 py-4 text-right shadow-[0_20px_50px_-35px_rgba(30,79,163,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Current Status</p>
            <p class="mt-2 text-base font-bold {{ $managedSite->is_active ? 'text-emerald-700' : 'text-slate-600' }}">{{ $managedSite->is_active ? 'Active' : 'Inactive' }}</p>
            <p class="mt-1 text-sm text-slate-500">Updated {{ optional($managedSite->updated_at)->format('M j, Y g:i A') ?: 'Unknown' }}</p>
        </div>
    </div>

    @if(session('admin_notice'))
        <div class="mb-6 rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
            {{ session('admin_notice') }}
        </div>
    @endif

    <div class="mb-6 rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Availability Control</h2>
                <p class="mt-1 text-sm text-slate-500">Use this quick action when the location should stay in history but stop accepting future attendance selections.</p>
            </div>

            {{-- This separate form prevents accidental status flips during normal content edits. --}}
            <form action="{{ route('admin.sites.status', $managedSite) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_active" value="{{ $managedSite->is_active ? 0 : 1 }}">
                <button type="submit" class="inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(15,23,42,0.55)] transition {{ $managedSite->is_active ? 'bg-slate-700 hover:bg-slate-800' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ $managedSite->is_active ? 'Deactivate Site' : 'Activate Site' }}
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.sites.update', $managedSite) }}" method="POST">
        @csrf
        @method('PATCH')
        @include('pages.admin.sites.partials.form-fields', [
            'managedSite' => $managedSite,
            'siteCoordinates' => $siteCoordinates,
            'submitLabel' => 'Save Site Changes',
        ])
    </form>

    <form action="{{ route('admin.sites.attendance-policy.update', $managedSite) }}" method="POST" class="mt-8 rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        @csrf
        @method('PATCH')

        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Attendance Policy</p>
                <h2 class="mt-2 text-xl font-bold text-[#1e4fa3] sm:text-2xl">WFH Anchor Limit</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">
                    This limit is checked when a student times out from a work-from-home day. If the student drifts too far from the original time-in location, the time-out is rejected.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="mt-5 rounded-[1.4rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="font-semibold">Please review the attendance policy value before saving.</p>
            </div>
        @endif

        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,0.85fr)]">
            <section>
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Allowed Movement From Time-In Point (Meters)</span>
                    <input
                        name="wfh_anchor_limit_m"
                        type="number"
                        min="1"
                        max="5000"
                        value="{{ old('wfh_anchor_limit_m', $wfhAnchorLimit ?? 20) }}"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white"
                    >
                    @error('wfh_anchor_limit_m') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                </label>

                <div class="mt-4 rounded-[1.4rem] border border-[#d7e2f5] bg-[#f7f9fc] px-4 py-4 text-sm text-slate-600">
                    <p class="font-semibold text-[#1e4fa3]">How this rule works</p>
                    <p class="mt-2">This value is currently treated as a global rule, but it is managed here so admins keep all site-related attendance controls in one place.</p>
                    <p class="mt-2">New WFH daily time records copy the latest value so older records keep the rule that was active when they started.</p>
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