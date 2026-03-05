@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center ' . $class]) }}>
    <div class="h-16 w-16 shrink-0 rounded-full bg-white sm:h-20 sm:w-20" style="box-shadow: 4px 4px 12px rgba(0,0,0,0.08);"></div>
    <h1 class="mt-3 text-xl font-bold uppercase tracking-wide sm:text-2xl" style="color: #1f4082;">OJT LOGS</h1>
</div>
