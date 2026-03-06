@props(['heading' => 'OJT LOGS', 'maxWidth' => 'max-w-2xl', 'headingClass' => '', 'spacious' => false])

<div class="mx-auto w-full {{ $maxWidth }}">
    <div class="flex flex-col items-center">
        {{-- White circle above card, overlapping top edge --}}
        <div
            class="h-20 w-20 shrink-0 rounded-full bg-white sm:h-24 sm:w-24"
            style="box-shadow: 0 4px 14px rgba(0,0,0,0.06); margin-bottom: -2.5rem; z-index: 10;"
        ></div>
        {{-- White rounded card: smooth shadow, rounded corners --}}
        <div
            class="w-full rounded-3xl bg-white pl-6 pr-6 {{ $spacious ? 'pt-20 pb-12 sm:pt-24 sm:pb-14' : 'pt-14 pb-8 sm:pt-16 sm:pb-10' }} sm:px-8"
            style="box-shadow: 0 4px 20px rgba(0,0,0,0.06);"
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
