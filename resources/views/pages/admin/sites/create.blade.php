<x-admin.layouts.dashboard title="Create Site" active="sites">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1e4fa3]">Site Management</p>
        <h1 class="mt-2 text-2xl font-black tracking-[0.08em] text-[#1e4fa3] sm:text-3xl">Add New Site</h1>
        <p class="mt-3 max-w-2xl text-sm text-slate-600">
            Create a new internship location with the geofence coordinates and radius future attendance checks will rely on.
        </p>
    </div>

    <form action="{{ route('admin.sites.store') }}" method="POST">
        @csrf
        @include('pages.admin.sites.partials.form-fields', [
            'managedSite' => null,
            'siteCoordinates' => $siteCoordinates ?? ['latitude' => null, 'longitude' => null],
            'submitLabel' => 'Create Site',
        ])
    </form>
</x-admin.layouts.dashboard>