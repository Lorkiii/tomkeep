<x-ojt-card heading="" maxWidth="max-w-md">
    {{-- Rejected icon --}}
    <div class="flex justify-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
            <svg class="h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>
    </div>

    <h2 class="mt-5 text-center text-xl font-bold tracking-wide" style="color: #1f4082;">
        Application Not Approved
    </h2>

    <p class="mt-3 text-center text-sm leading-relaxed text-slate-600">
        Unfortunately, your account registration was not approved by the administrator.
    </p>

    @if($reason)
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-red-400">Admin Remarks</p>
            <p class="text-sm leading-relaxed text-red-800">{{ $reason }}</p>
        </div>
    @endif

    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <p class="text-center text-sm text-slate-600">
            If you believe this is an error, please contact your OJT coordinator.
        </p>
    </div>

    <div class="mt-8">
        <button
            wire:click="logout"
            class="w-full rounded-xl px-4 py-3 font-medium uppercase tracking-wide text-white shadow transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2"
            style="background-color: #1f4082;"
        >
            Back to Login
        </button>
    </div>
</x-ojt-card>
