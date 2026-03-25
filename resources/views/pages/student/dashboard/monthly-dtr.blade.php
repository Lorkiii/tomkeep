<x-student.layouts.dashboard title="My Monthly DTR" active="dtr">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-8 w-8 shrink-0 rounded-full bg-slate-300"></span>
            <h1 class="text-xl font-semibold text-slate-800">OJT LOGS</h1>
        </div>
        @if(isset($currentOjtUser) && $currentOjtUser)
            <p class="text-sm font-medium text-slate-700">Howdy, {{ $currentOjtUser['first_name'] ?? 'User' }}!</p>
        @endif
    </div>

    <h2 class="mb-6 text-lg font-semibold text-slate-900">My Monthly DTR</h2>

    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <p class="mb-4 text-center text-lg font-bold text-slate-800">{{ now()->format('F Y') }}</p>
        <div class="grid grid-cols-7 gap-1 text-center text-sm">
            @foreach(['S', 'M', 'T', 'W', 'Th', 'F', 'S'] as $day)
                <span class="py-2 font-medium text-slate-500">{{ $day }}</span>
            @endforeach
            @php
                // Current month being displayed in the calendar.
                $month = now();

                // Carbon::SUNDAY tells startOfWeek which weekday should be treated as the first day.
                // That is why this line uses \Carbon\Carbon::SUNDAY.
                $firstDay = $month->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);

                // dayOfWeek returns 0 for Sunday through 6 for Saturday.
                // We use that number to print leading empty cells before day 1.
                $dayOfWeek = $month->copy()->startOfMonth()->dayOfWeek;
            @endphp
            @for($i = 0; $i < $dayOfWeek; $i++)
                <span class="py-2 text-slate-300">—</span>
            @endfor
            @for($d = 1; $d <= $month->daysInMonth; $d++)
                <span class="rounded py-2 {{ $d == $month->day ? 'bg-[#1e3a5f] text-white' : 'text-slate-700' }}">{{ $d }}</span>
            @endfor
        </div>
        <div class="mt-6 flex justify-between">
            <div class="flex gap-2">
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Previous</button>
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Next</button>
            </div>
            <button type="button" class="rounded-xl bg-[#1e3a5f] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#152a47]">Generate PDF</button>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-2">
        <p class="text-xs text-slate-400">Copyright © {{ date('Y') }}. Powered by</p>
        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-500 shadow-sm hover:bg-slate-50" aria-label="Toggle theme">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </button>
    </div>
</x-student.layouts.dashboard>