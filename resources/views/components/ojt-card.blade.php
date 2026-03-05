@props(['heading' => 'OJT LOGS', 'maxWidth' => 'max-w-2xl', 'headingClass' => ''])

<div class="mx-auto w-full {{ $maxWidth }}">
    <div class="flex flex-col items-center">
        {{-- White circle above card, overlapping top edge --}}
        <div
            class="h-20 w-20 shrink-0 rounded-full bg-white sm:h-24 sm:w-24"
            style="box-shadow: 4px 4px 12px rgba(0,0,0,0.08); margin-bottom: -2.5rem; z-index: 10;"
        ></div>
        {{-- White rounded card with soft shadow --}}
        <div
            class="w-full rounded-2xl bg-white pt-14 pb-8 pl-6 pr-6 sm:pt-16 sm:px-8 sm:pb-10"
            style="box-shadow: 0 4px 20px rgba(0,0,0,0.08);"
        >
            @if($heading)
                <h1 class="mb-4 text-center text-xl font-bold sm:text-2xl {{ trim((string) $headingClass) !== '' ? $headingClass : 'uppercase tracking-wide' }}" @if(trim((string) $headingClass) === '') style="color: #1f4082;" @endif>
                    {{ $heading }}
                </h1>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
