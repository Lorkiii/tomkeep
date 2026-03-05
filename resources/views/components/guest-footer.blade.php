<footer class="mt-8 flex flex-col items-center gap-0.5 text-center text-xs text-slate-400">
    <p class="flex items-center justify-center gap-1.5">
        Copyright &copy; {{ date('Y') }}. Powered by
        <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-slate-300 text-slate-500" aria-hidden="true">
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
        @if(isset($extra))
            <span>{{ $extra }}</span>
        @endif
    </p>
</footer>
