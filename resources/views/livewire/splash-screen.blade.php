{{-- Splash - Web: white bg, solid white circle logo, OJT LOGS (digital style) --}}
<div
    class="flex min-h-screen flex-col items-center justify-center bg-white"
    x-data="{
        visible: false,
        redirectDelay: 2000, 
    }"
    x-init="
        $nextTick(() => { visible = true; });
        setTimeout(() => { $wire.redirectToTerms(); }, redirectDelay);
    "
>
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-700"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        class="flex flex-col items-center"
    >
        {{-- Solid White Circle Logo with soft shadow --}}
        <div class="h-32 w-32 rounded-full bg-white shadow-[0_10px_30px_rgba(0,0,0,0.1)] mb-4 flex items-center justify-center">
            {{-- Maaari mong ilagay ang iyong inner logo image dito --}}
        </div>

        {{-- OJT LOGS Branding: Blue digital style font --}}
        <h1
            class="mt-2 text-2xl font-bold uppercase tracking-[0.25em]"
            style="color: #1a4b8c; font-family: 'Orbitron', sans-serif;"
        >
            OJT LOGS
        </h1>
    </div>
</div>