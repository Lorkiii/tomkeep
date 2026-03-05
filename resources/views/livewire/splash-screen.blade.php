{{-- Splash screen: white circle with shadow + OJT LOGS, fade-in, then redirect --}}
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
        {{-- White circle with soft gray drop shadow (down and right) --}}
        <div
            class="h-24 w-24 shrink-0 rounded-full bg-white sm:h-28 sm:w-28"
            style="box-shadow: 4px 4px 12px rgba(0,0,0,0.08);"
        ></div>
        <h1 class="mt-6 text-xl font-bold uppercase tracking-wide text-[#1f4082] sm:text-2xl">OJT LOGS</h1>
    </div>
</div>
