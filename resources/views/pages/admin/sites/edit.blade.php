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
</x-admin.layouts.dashboard>