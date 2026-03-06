<x-layouts.dashboard title="Dashboard" active="dashboard">
    @php
        $today = now();
        $hour = (int) $today->format('H');
        $isDay = $hour >= 6 && $hour < 18;
    @endphp

    <div class="mx-auto max-w-5xl rounded-3xl bg-white px-10 py-8 shadow-[0_12px_40px_rgba(0,0,0,0.12)]">
        <div class="mb-6 flex items-start justify-between">
            <div class="flex flex-1 justify-center">
                <div class="flex flex-col items-center">
                    <div class="mb-3 h-16 w-16 rounded-full border-2 border-[#1C4DA1] bg-white shadow-[0_6px_18px_rgba(0,0,0,0.15)]"></div>
                    <h1 class="font-['Orbitron',theme('font.sans')] text-xl font-bold tracking-wide text-[#1C4DA1]">
                        OJT LOGS
                    </h1>
                </div>
            </div>
            @if(isset($currentOjtUser) && $currentOjtUser)
                <p class="ml-4 text-sm font-semibold text-[#285DAB]">
                    Howdy, {{ $currentOjtUser['first_name'] ?? 'User' }}!
                </p>
            @endif
        </div>

        <p class="mb-6 text-lg font-semibold text-[#285DAB]">
            Today is {{ $today->format('F j, Y') }}
        </p>

        <div class="mb-8 grid gap-6 md:grid-cols-[minmax(0,2.1fr)_minmax(0,1.6fr)]">
            <section class="rounded-2xl border border-[#d3dfef] bg-gradient-to-br from-[#eef4ff] via-[#f2f6ff] to-[#e3f0ff] px-6 py-5 shadow-[0_8px_24px_rgba(0,0,0,0.08)]">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-[#285DAB]">Your Progress</h2>
                    <span class="text-sm font-semibold text-[#285DAB]">{{ $progressPercent ?? 0 }}%</span>
                </div>
                <div class="mb-4 h-3 w-full overflow-hidden rounded-full bg-white/70">
                    <div
                        class="h-full rounded-full bg-[#F8D24B] transition-all"
                        style="width: {{ min(max($progressPercent ?? 0, 0), 100) }}%"
                    ></div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-[#364152]">
                    <span>Remaining Hours: {{ $remainingHours ?? 0 }} h</span>
                    @if(isset($requiredHours) && $requiredHours > 0 && ($remainingHours ?? 0) > 0)
                        @php
                            $daysLeft = (int) ceil(($remainingHours ?? 0) / 8);
                            $estFinish = $today->copy()->addDays($daysLeft);
                        @endphp
                        <span>Est. Finish Date: {{ $estFinish->format('F j, Y') }}</span>
                    @else
                        <span>Est. Finish Date: —</span>
                    @endif
                </div>
            </section>

            <section class="flex flex-col justify-center rounded-2xl border border-[#d3dfef] bg-[#f3f6fb] px-6 py-5 text-center shadow-[0_8px_24px_rgba(0,0,0,0.08)]">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-[#6b7ba1]">This day</p>
                        <p class="mt-1 text-2xl font-bold text-[#285DAB]">
                            {{ $hoursThisDay ?? 0 }}
                            <span class="ml-1 text-xs font-normal text-[#6b7ba1]">HRS</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-[#6b7ba1]">This Week</p>
                        <p class="mt-1 text-2xl font-bold text-[#285DAB]">
                            {{ $hoursThisWeek ?? 0 }}
                            <span class="ml-1 text-xs font-normal text-[#6b7ba1]">HRS</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-[#6b7ba1]">This Month</p>
                        <p class="mt-1 text-2xl font-bold text-[#285DAB]">
                            {{ $hoursThisMonth ?? 0 }}
                            <span class="ml-1 text-xs font-normal text-[#6b7ba1]">HRS</span>
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <section>
            <h2 class="mb-3 text-base font-semibold text-[#285DAB]">Activity Logs</h2>
            <div class="overflow-hidden rounded-2xl border border-[#d3dfef] bg-white shadow-[0_8px_24px_rgba(0,0,0,0.06)]">
                @if(!empty($activityLogs))
                    <ul class="divide-y divide-[#e7edf7]">
                        @foreach($activityLogs as $log)
                            <li class="flex items-center justify-between px-6 py-3 text-sm text-[#364152]">
                                <span>
                                    {{ ($log['type'] ?? '') === 'time_out' ? 'Time-Out Work on Office' : 'Time-In Work on Office' }}
                                </span>
                                <span class="text-xs font-medium text-[#6b7ba1]">
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
                    <p class="px-6 py-8 text-center text-sm text-[#6b7ba1]">
                        No activity logs yet. Time-in when you start work to record your hours.
                    </p>
                @endif
            </div>
        </section>

        <div class="mt-8 flex items-center justify-between">
            <p class="text-xs text-[#96a3c2]">
                Copyright © {{ date('Y') }}. Powered by
            </p>
            <button
                type="button"
                class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-[#2f56b0] to-[#1c3f82] text-white shadow-[0_8px_24px_rgba(0,0,0,0.25)]"
                aria-label="Time in or out"
                id="time-toggle-button"
            >
                @if($isDay)
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4.5a1 1 0 0 1 1 1V7a1 1 0 1 1-2 0V5.5a1 1 0 0 1 1-1zm0 11a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zm7-3.5a1 1 0 0 1-1 1h-1.5a1 1 0 1 1 0-2H18a1 1 0 0 1 1 1zm-11.5 0a1 1 0 0 1-1 1H5a1 1 0 1 1 0-2h1.5a1 1 0 0 1 1 1zm9.192 4.95a1 1 0 0 1-1.414 0l-1.06-1.06a1 1 0 0 1 1.414-1.415l1.06 1.06a1 1 0 0 1 0 1.415zm-9.9-9.9a1 1 0 0 1-1.414 0L4.317 6.94A1 1 0 0 1 5.73 5.525l1.06 1.06a1 1 0 0 1 0 1.415zm0 9.9-1.06 1.06A1 1 0 1 1 4.317 16.6l1.06-1.06a1 1 0 0 1 1.415 1.414zm9.9-9.9-1.06 1.06A1 1 0 1 1 15.22 6.94l1.06-1.06A1 1 0 0 1 17.694 7.3z"/>
                    </svg>
                @else
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 12.79A9 9 0 0 1 11.21 3 7 7 0 1 0 21 12.79z"/>
                    </svg>
                @endif
            </button>
        </div>
    </div>
</x-layouts.dashboard>

