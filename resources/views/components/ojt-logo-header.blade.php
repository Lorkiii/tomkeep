@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center ' . $class]) }}>
    {{-- Circular light gray outline (logo placeholder) --}}
    <div class="h-[70px] w-[70px] shrink-0 rounded-full border-2 border-slate-300 bg-white"></div>
    <h1 class="mt-3 text-xl font-bold uppercase tracking-wide text-slate-900 sm:text-2xl">OJT LOGS</h1>
</div>
