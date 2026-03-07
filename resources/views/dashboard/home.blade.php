{{-- Student dashboard: progress summary, computed stats, and activity logs. --}}
<x-layouts.dashboard title="Dashboard" active="dashboard">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-8 w-8 shrink-0 rounded-full bg-slate-300"></span>
            <h1 class="text-xl font-semibold text-slate-800">OJT LOGS</h1>
        </div>
        @if(isset($currentOjtUser) && $currentOjtUser)
            <p class="text-sm font-medium text-slate-700">Howdy, {{ $currentOjtUser['first_name'] ?? 'User' }}!</p>
        @endif
    </div>

    <p class="mb-6 text-sm text-slate-600">Today is {{ now()->format('l, F j, Y') }}</p>

    {{-- Your Progress card --}}
    <section class="mb-6 rounded-lg border border-slate-200 bg-slate-100/80 p-4 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-900">Your Progress</h2>
            <span class="text-sm font-medium text-slate-700">{{ $progressPercent ?? 0 }}%</span>
        </div>
        <div class="mb-3 h-3 w-full overflow-hidden rounded-full bg-slate-200">
            <div class="h-full rounded-full bg-amber-400 transition-all" style="width: {{ $progressPercent ?? 0 }}%"></div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-slate-700">
            <span>Remaining Hours: {{ $remainingHours ?? 0 }} h</span>
            @if(isset($requiredHours) && $requiredHours > 0 && ($remainingHours ?? 0) > 0)
                @php
                    $daysLeft = (int) ceil(($remainingHours ?? 0) / 8);
                    $estFinish = now()->addDays($daysLeft);
                @endphp
                <span>Est. Finish Date: {{ $estFinish->format('F j, Y') }}</span>
            @else
                <span>Est. Finish Date: —</span>
            @endif
        </div>
    </section>

    {{-- Summary cards (light gray) --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-slate-100/80 p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">This day</p>
            <p class="mt-1 text-xl font-bold text-slate-900">{{ $hoursThisDay ?? 0 }} <span class="text-sm font-normal text-slate-600">HRS</span></p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-slate-100/80 p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">This Week</p>
            <p class="mt-1 text-xl font-bold text-slate-900">{{ $hoursThisWeek ?? 0 }} <span class="text-sm font-normal text-slate-600">HRS</span></p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-slate-100/80 p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">This Month</p>
            <p class="mt-1 text-xl font-bold text-slate-900">{{ $hoursThisMonth ?? 0 }} <span class="text-sm font-normal text-slate-600">HRS</span></p>
        </div>
    </div>

    {{-- Activity Logs --}}
    <section>
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Activity Logs</h2>
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            @if(!empty($activityLogs))
                <ul class="divide-y divide-slate-100">
                    @foreach($activityLogs as $log)
                        <li class="flex items-center justify-between px-4 py-3 text-sm text-slate-700">
                            <span>
                                {{ ($log['type'] ?? '') === 'time_out' ? 'Time-Out' : 'Time-In' }}
                                {{ $log['label'] ?? 'Work on Office' }}
                            </span>
                            <span class="text-slate-500">
                                @if(!empty($log['at']))
                                    @php
                                        try {
                                            $dt = \Carbon\Carbon::parse($log['at']);
                                            echo $dt->format('g:iA') . ' | ' . $dt->format('F j, Y');
                                        } catch (\Exception $e) {
                                            echo $log['at'];
                                        }
                                    @endphp
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="px-4 py-8 text-center text-sm text-slate-500">No activity logs yet. Time-in when you start work to record your hours.</p>
            @endif
        </div>
    </section>

    <div class="mt-8 flex items-center justify-end gap-2">
        <p class="text-xs text-slate-400">Copyright © {{ date('Y') }}. Powered by</p>
        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-slate-500 shadow-sm hover:bg-slate-50" aria-label="Toggle theme">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </button>
    </div>
</x-layouts.dashboard>
