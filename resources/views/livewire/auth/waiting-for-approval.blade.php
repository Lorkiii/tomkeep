<x-ojt-card heading="" maxWidth="max-w-md">
    {{-- Hourglass icon --}}
    <div class="flex justify-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full" style="background-color: #fef3c7;">
            <svg class="h-8 w-8 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>

    <h2 class="mt-5 text-center text-xl font-bold tracking-wide" style="color: #1f4082;">
        Waiting for Approval
    </h2>

    <p class="mt-3 text-center text-sm leading-relaxed text-slate-600">
        Your profile has been submitted and is currently under review by an administrator.
        You will be able to access your dashboard once your account is approved.
    </p>

    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
        <p class="text-center text-sm font-medium text-amber-800">
            Please check back later or contact your OJT coordinator for updates.
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
