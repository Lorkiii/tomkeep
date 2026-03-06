{{-- Minimal splash: white bg, incomplete circle arc, OJT LOGS (digital style), then redirect to terms --}}
<div
    class="flex min-h-screen flex-col items-center justify-center bg-white"
    x-data="{
        visible: false,
        redirectDelay: 2000, // 2 seconds before redirecting to Terms
    }"
    x-init="
        $nextTick(() => { visible = true; });
        setTimeout(() => { $wire.redirectToTerms(); }, redirectDelay);
    "
>
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="flex flex-col items-center"
    >
        {{-- Incomplete circle: 3/4 arc, open at bottom-right, soft gray shadow --}}
        <div class="relative h-28 w-28 sm:h-32 sm:w-32" style="filter: drop-shadow(4px 4px 12px rgba(0,0,0,0.18));">
            <svg class="h-full w-full" viewBox="0 0 100 100" fill="none">
                <path
                    d="M 85 50 A 35 35 0 1 1 50 85"
                    stroke="#ffffff"
                    fill="none"
                    stroke-width="12"
                    stroke-linecap="round"
                />
            </svg>
        </div>
        <h1
            class="mt-6 text-xl font-bold uppercase tracking-widest sm:text-2xl"
            style="color: #1a365d; font-family: 'Orbitron', ui-sans-serif, system-ui, sans-serif; letter-spacing: 0.2em;"
        >
            OJT LOGS
        </h1>
    </div>
</div>
