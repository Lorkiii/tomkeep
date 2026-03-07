<x-layouts.dashboard title="Account Settings" active="account">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-8 w-8 shrink-0 rounded-full bg-slate-300"></span>
            <h1 class="text-xl font-semibold text-slate-800">OJT LOGS</h1>
        </div>
        @if(isset($currentOjtUser) && $currentOjtUser)
            <p class="text-sm font-medium text-slate-700">Howdy, {{ $currentOjtUser['first_name'] ?? 'User' }}!</p>
        @endif
    </div>

    <h2 class="mb-6 text-lg font-semibold text-slate-900">Account Settings</h2>

    @if(isset($currentOjtUser) && $currentOjtUser)
        @php
            $u = $currentOjtUser;
            $addr = is_array($u['address'] ?? null) ? $u['address'] : [];
        @endphp
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Gender</dt>
                    <dd class="mt-1 text-slate-700">{{ $u['gender'] ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Birthday</dt>
                    <dd class="mt-1 text-slate-700">
                        @if(!empty($u['date_of_birth']))
                            {{ \Carbon\Carbon::parse($u['date_of_birth'])->format('m/d/Y') }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Address</dt>
                    <dd class="mt-1 text-slate-700">
                        @php
                            // Show address using new PSGC-backed structure, with fallback keys for old records.
                            $parts = array_filter([
                                $addr['province'] ?? $addr['state_province'] ?? '',
                                $addr['municipality'] ?? $addr['city'] ?? '',
                                $addr['barangay'] ?? '',
                                $addr['street_house_number'] ?? trim(($addr['street'] ?? '') . ' ' . ($addr['house_number'] ?? '')),
                            ]);
                        @endphp
                        {{ implode(', ', $parts) ?: '—' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Contact Number</dt>
                    <dd class="mt-1 text-slate-700">{{ $u['contact_number'] ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">University Attended</dt>
                    <dd class="mt-1 text-slate-700">{{ $u['school_attended'] ?? '—' }}</dd>
                </div>
            </dl>
            <div class="mt-6">
                <a href="{{ route('profile.setup') }}" class="inline-flex rounded-xl bg-[#1e3a5f] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#152a47]">
                    Edit
                </a>
            </div>
        </div>
    @endif

    <div class="mt-8 flex items-center justify-end gap-2">
        <p class="text-xs text-slate-400">Copyright © {{ date('Y') }}. Powered by</p>
        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-500 shadow-sm hover:bg-slate-50" aria-label="Toggle theme">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </button>
    </div>
</x-layouts.dashboard>
