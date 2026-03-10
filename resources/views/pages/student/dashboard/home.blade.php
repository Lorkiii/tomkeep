{{-- Student dashboard: progress summary, computed stats, and activity logs. --}}
<x-student.layouts.dashboard title="Dashboard" active="dashboard">
    {{-- Success notice shown after a Livewire attendance action finishes. --}}
    @if(session('dashboard_notice'))
        <div
            class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700 shadow-[0_18px_40px_-30px_rgba(16,185,129,0.7)]">
            {{ session('dashboard_notice') }}
        </div>
    @endif

    {{-- Top heading row: brand on the left, greeting on the right. --}}
    <div class="mb-6 flex items-start justify-between gap-4 xl:mb-8">
        <div class="flex items-center gap-3 sm:gap-4">
            <span
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#cfdcf5] bg-white text-sm font-extrabold tracking-[0.18em] text-[#1e4fa3] shadow-sm sm:h-12 sm:w-12">OJ</span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Internship Attendance</p>
                <h1 class="text-2xl font-extrabold tracking-[0.14em] text-[#1e4fa3] sm:text-[2rem]">OJT LOGS</h1>
            </div>
        </div>
        @if(isset($currentOjtUser) && $currentOjtUser)
            <p class="pt-2 text-right text-sm font-semibold text-[#1e4fa3] sm:text-base">Howdy,
                {{ $currentOjtUser['first_name'] ?? 'User' }}!</p>
        @endif
    </div>

    {{-- Date headline. This mirrors the reference layout. --}}
    <p class="mb-6 text-xl font-bold text-[#214e9d] sm:text-3xl xl:mb-8">Today is {{ now()->format('F j, Y') }}</p>

    {{--
    Top summary grid.

    Left side:
    progress card

    Right side:
    this day / week / month card
    --}}
    <div class="mb-8 grid gap-5 lg:grid-cols-[minmax(0,1.4fr)_minmax(320px,0.95fr)] xl:mb-10 xl:gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(430px,0.95fr)] 2xl:gap-8 2xl:grid-cols-[minmax(0,1.7fr)_minmax(500px,0.98fr)]">
        {{-- Progress card. --}}
        <section
            class="rounded-[1.75rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.45)] sm:p-6 xl:p-7 2xl:p-8">
            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-[#1e4fa3] xl:text-2xl">Your Progress</h2>
                <span
                    class="rounded-full border border-[#cfdbf1] bg-white/80 px-3 py-1 text-sm font-bold text-[#1e4fa3]">{{ $progressPercent ?? 0 }}%</span>
            </div>

            {{--
            Semantic progress element.
            We style it in app.css so we don't need inline width rules here.
            --}}
            <progress class="dashboard-progress mb-5 h-3.5 w-full overflow-hidden rounded-full bg-white/90 shadow-inner"
                value="{{ $progressPercent ?? 0 }}" max="100">{{ $progressPercent ?? 0 }}%</progress>

            <div class="grid gap-4 text-sm text-slate-600 sm:grid-cols-2 xl:gap-6 xl:text-base">
                {{-- Remaining required internship hours. --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Remaining Hours</p>
                    <p class="mt-2 text-3xl font-bold text-[#1e4fa3] xl:text-4xl 2xl:text-[2.8rem]">{{ $remainingHours ?? 0 }} h</p>
                </div>

                {{-- Estimated finish date, based on 8 hours per day. --}}
                <div class="text-left sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Est. Finish Date</p>
                    @if(isset($requiredHours) && $requiredHours > 0 && ($remainingHours ?? 0) > 0)
                        @php
                            // Very simple projection: remaining hours divided by 8-hour days.
                            $daysLeft = (int) ceil(($remainingHours ?? 0) / 8);
                            $estFinish = now()->addDays($daysLeft);
                        @endphp
                        <p class="mt-2 text-3xl font-bold text-[#1e4fa3] xl:text-4xl 2xl:text-[2.8rem]">{{ $estFinish->format('M j, Y') }}</p>
                    @else
                        <p class="mt-2 text-3xl font-bold text-[#1e4fa3] xl:text-4xl 2xl:text-[2.8rem]">Complete</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Compact summary card for day/week/month totals. --}}
        <section
            class="rounded-[1.75rem] border border-slate-200 bg-[linear-gradient(180deg,rgba(255,255,255,0.96),rgba(244,244,246,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.45)] sm:p-6 xl:p-7 2xl:p-8">
            <div class="grid grid-cols-3 gap-4 text-center xl:gap-5 2xl:gap-6">
                {{-- Hours worked today. --}}
                <div class="rounded-[1.4rem] px-2 py-4">
                    <p class="text-sm font-semibold text-[#1e4fa3]">This day</p>
                    <p class="mt-2 text-5xl font-bold leading-none text-[#1e4fa3] xl:text-6xl 2xl:text-7xl">{{ $hoursThisDay ?? 0 }}</p>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#1e4fa3]">HRS</p>
                </div>

                {{-- Hours worked during the current week. --}}
                <div class="rounded-[1.4rem] px-2 py-4">
                    <p class="text-sm font-semibold text-[#1e4fa3]">This Week</p>
                    <p class="mt-2 text-5xl font-bold leading-none text-[#1e4fa3] xl:text-6xl 2xl:text-7xl">{{ $hoursThisWeek ?? 0 }}</p>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#1e4fa3]">HRS</p>
                </div>

                {{-- Hours worked during the current month. --}}
                <div class="rounded-[1.4rem] px-2 py-4">
                    <p class="text-sm font-semibold text-[#1e4fa3]">This Month</p>
                    <p class="mt-2 text-5xl font-bold leading-none text-[#1e4fa3] xl:text-6xl 2xl:text-7xl">{{ $hoursThisMonth ?? 0 }}</p>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#1e4fa3]">HRS</p>
                </div>
            </div>
        </section>
    </div>

    {{--
    Recent logs are intentionally daily-only.
    Older records belong in the Monthly DTR page, not on the dashboard.
    --}}
    <section
        class="rounded-[1.9rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6 xl:p-7 2xl:p-8">
        <div class="mb-4 border-b border-[#f1c74a] pb-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-[#1e4fa3] xl:text-[2rem]">Recent Logs</h2>
                    <p class="mt-1 text-sm text-slate-500">Showing today's attendance records only. Older entries stay
                        in My Monthly DTR.</p>
                </div>
                <a href="{{ route('monthly.dtr') }}"
                    class="inline-flex rounded-full border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-2 text-sm font-semibold text-[#1e4fa3] transition hover:border-[#1e4fa3] hover:bg-white">
                    View Monthly DTR
                </a>
            </div>
        </div>

        {{-- Scrollable log list to protect the page layout when many entries exist. --}}
        <div class="rounded-[1.5rem] border border-slate-200/70 bg-[#fbfbfc]">
            @if(!empty($activityLogs))
                <ul class="max-h-[24rem] divide-y divide-slate-200 overflow-y-auto xl:max-h-[30rem] 2xl:max-h-[34rem]">
                    @foreach($activityLogs as $log)
                        {{-- Each row shows the action label and the recorded date/time. --}}
                        <li
                            class="grid gap-1 px-4 py-4 text-sm text-slate-700 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center sm:gap-4 sm:px-5 xl:px-6 xl:py-5 xl:text-base">
                            <span class="font-semibold text-[#1f4f9c]">
                                {{ $log['label'] ?? 'Attendance Activity' }}
                            </span>
                            <span class="text-sm font-medium text-[#2f5fae] sm:text-right">
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
                {{-- Friendly empty state before the first action is recorded. --}}
                <p class="px-4 py-10 text-center text-sm text-slate-500 xl:px-6 xl:py-14 xl:text-base">No attendance logs for today yet. Use the action
                    button to record your first entry.</p>
            @endif
        </div>
    </section>

    {{-- Footer area kept simple and low-contrast so it doesn't compete with content. --}}
    <div class="mt-8 flex items-center justify-center gap-2 pb-14 text-center sm:justify-end xl:mt-10">
        <p class="text-sm text-slate-400">Copyright © {{ date('Y') }}. Powered by</p>
        <span class="text-sm font-semibold text-[#1e4fa3]">OJT Logs</span>
    </div>

  
    {{-- Floating attendance button rendered as its own Livewire component. --}}
    <livewire:dashboard-quick-action />
</x-student.layouts.dashboard>