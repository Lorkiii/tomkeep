@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center ' . $class]) }}>
    {{-- Logo placeholder circle (slot for future logo) --}}
    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white shadow-md sm:h-28 sm:w-28">
        @if(isset($logo))
            {{ $logo }}
        @endif
    </div>
    <h1 class="mt-4 text-xl font-bold tracking-wide text-[#1e3a5f] sm:text-2xl">OJT LOGS</h1>
</div>
