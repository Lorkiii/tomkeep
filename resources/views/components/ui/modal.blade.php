@props([
    'title',
    'maxWidth' => 'max-w-sm',
])

{{--
    Shared modal shell.

    This component is intentionally presentational only.
    It does not decide business rules or state transitions.
--}}
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="shared-modal-title">
    <div {{ $attributes->merge(['class' => 'w-full ' . $maxWidth . ' rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_32px_90px_-45px_rgba(15,23,42,0.75)] sm:p-7']) }}>
        <div class="flex gap-4">
            @isset($icon)
                <div class="shrink-0">
                    {{ $icon }}
                </div>
            @endisset

            <div class="min-w-0 flex-1">
                <h3 id="shared-modal-title" class="text-lg font-bold text-slate-900 sm:text-xl">{{ $title }}</h3>

                <div class="mt-3 text-sm leading-6 text-slate-600 sm:text-[15px]">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @isset($actions)
            <div class="mt-6 flex gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>