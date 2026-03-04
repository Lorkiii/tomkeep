{{-- Splash screen: logo + title, fade-in animation, then redirect to Terms without full page reload --}}
<div
    class="flex min-h-screen flex-col items-center justify-center"
    x-data="{
        visible: false,
        redirectDelay: 3000
    }"
    x-init="
        // Fade-in content after mount
        $nextTick(() => { visible = true; });
        // Redirect via Livewire (navigate: true) so the page does not fully reload
        setTimeout(() => { $wire.redirectToTerms(); }, redirectDelay);
    "
>
    {{-- Minimal animation: fade-in + slight scale --}}
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="flex flex-col items-center"
    >
        <x-ojt-logo-header class="mb-0" />
    </div>
</div>
