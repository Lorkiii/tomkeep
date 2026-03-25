<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <section class="rounded-[1.6rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">All Sites</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['all'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Every internship site configured for attendance use.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-[linear-gradient(180deg,rgba(255,255,255,0.96),rgba(244,244,246,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Active</p>
            <p class="mt-3 text-4xl font-black text-emerald-700">{{ $counts['active'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Sites that can be used for current student attendance.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Inactive</p>
            <p class="mt-3 text-4xl font-black text-[#9f3f1d]">{{ $counts['inactive'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Sites kept for history but currently unavailable for new use.</p>
        </section>

        <section class="rounded-[1.6rem] border border-[#d7e2f5] bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Average Radius</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $counts['averageRadius'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Typical geofence distance in meters across all sites.</p>
        </section>
    </div>

    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Configured Sites</h2>
                <p class="mt-1 text-sm text-slate-500">Filter by status or search by company name to maintain the geofence directory.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[220px_minmax(0,320px)]">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</span>
                    <select wire:model.live="statusFilter" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm font-semibold text-[#1e4fa3] outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                        <option value="all">All Sites</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Search sites</span>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Search</span>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Company name"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3] focus:bg-white">
                </label>
            </div>
        </div>

        <div class="mt-6 space-y-4 lg:hidden">
            @forelse($sites as $site)
            @php
            // Address fragments stay optional because some sites may be created before full address details are known.
            $addressLabel = trim(collect([
            $site->address['street_address'] ?? null,
            $site->address['barangay'] ?? null,
            $site->address['municipality'] ?? null,
            $site->address['province'] ?? null,
            ])->filter()->implode(', '));
            @endphp
            <article wire:key="managed-site-card-{{ $site->id }}" class="rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-4 shadow-[0_20px_45px_-35px_rgba(15,23,42,0.35)]">
                <div class="flex flex-col gap-4">
                    <div>
                        <p class="text-base font-bold text-[#1e4fa3]">{{ $site->company_name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $addressLabel !== '' ? $addressLabel : 'Address not yet provided' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $site->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $site->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="rounded-full bg-[#e8f0ff] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#1e4fa3]">
                            {{ $site->allowed_radius_m }} m radius
                        </span>
                    </div>

                    <div class="grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                        <p><span class="font-semibold text-slate-500">Created:</span> {{ optional($site->created_at)->format('M j, Y g:i A') ?: 'Unknown' }}</p>
                        <p><span class="font-semibold text-slate-500">Updated:</span> {{ optional($site->updated_at)->format('M j, Y g:i A') ?: 'Unknown' }}</p>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.sites.edit', $site) }}" wire:navigate class="inline-flex items-center justify-center rounded-full border border-[#d5e0f0] bg-white px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">Manage</a>
                    </div>
                </div>
            </article>
            @empty
            <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-14 text-center text-sm text-slate-500">No sites matched the current filters.</div>
            @endforelse
        </div>

        <div class="mt-6 hidden overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-white/80 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="px-4 py-4">Company</th>
                            <th class="px-4 py-4">Address</th>
                            <th class="px-4 py-4">Radius</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-4 py-4">Updated</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-600">
                        @forelse($sites as $site)
                        @php
                        $addressLabel = trim(collect([
                        $site->address['street_address'] ?? null,
                        $site->address['barangay'] ?? null,
                        $site->address['municipality'] ?? null,
                        $site->address['province'] ?? null,
                        ])->filter()->implode(', '));
                        @endphp
                        <tr wire:key="managed-site-{{ $site->id }}">
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-[#1e4fa3]">{{ $site->company_name }}</p>
                                <p class="mt-1 text-xs text-slate-400">Created {{ optional($site->created_at)->format('M j, Y') ?: 'Unknown' }}</p>
                            </td>
                            <td class="px-4 py-4 align-top text-slate-500">{{ $addressLabel !== '' ? $addressLabel : 'Address not yet provided' }}</td>
                            <td class="px-4 py-4 align-top text-slate-500">{{ $site->allowed_radius_m }} meters</td>
                            <td class="px-4 py-4 align-top">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $site->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $site->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top text-slate-500">{{ optional($site->updated_at)->format('M j, Y g:i A') ?: 'Unknown' }}</td>
                            <td class="px-4 py-4 align-top text-right">
                                <a href="{{ route('admin.sites.edit', $site) }}" wire:navigate class="inline-flex rounded-full border border-[#d5e0f0] bg-white px-3 py-2 text-xs font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3]">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center text-sm text-slate-500">No sites matched the current filters.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($sites->hasPages())
        <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-center text-sm text-slate-500 sm:text-left">Showing {{ $sites->firstItem() }}-{{ $sites->lastItem() }} of {{ $sites->total() }} sites.</p>
            {{ $sites->links() }}
        </div>
        @endif
    </section>
</div>