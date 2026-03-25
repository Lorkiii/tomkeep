<div class="space-y-6" wire:key="reports-container">
    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Attendance Reports</h2>
                <p class="mt-1 text-sm text-slate-500">Filter by date range, status, and search keywords, then export to CSV.</p>
            </div>
            <div class="inline-flex rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] p-1">
                <button wire:click="setViewMode('table')" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $viewMode === 'table' ? 'bg-[#1e4fa3] text-white shadow-md' : 'text-slate-600 hover:text-[#1e4fa3]' }}">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Table
                    </span>
                </button>
                <button wire:click="setViewMode('calendar')" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $viewMode === 'calendar' ? 'bg-[#1e4fa3] text-white shadow-md' : 'text-slate-600 hover:text-[#1e4fa3]' }}">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Calendar
                    </span>
                </button>
            </div>
        </div>
        <div class="mt-5 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 mr-2">Quick Select:</span>
            @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'last_month' => 'Last Month'] as $preset => $label)
            <button wire:click="applyDatePreset('{{ $preset }}')" wire:key="preset-{{ $preset }}" class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $datePreset === $preset ? 'bg-[#1e4fa3] text-white shadow-md' : 'border border-[#d5e0f0] bg-white text-slate-600 hover:border-[#1e4fa3] hover:text-[#1e4fa3]' }}">{{ $label }}</button>
            @endforeach
            <button wire:click="setCustomPreset()" wire:key="preset-custom" class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $datePreset === 'custom' ? 'bg-[#1e4fa3] text-white shadow-md' : 'border border-[#d5e0f0] bg-white text-slate-600 hover:border-[#1e4fa3] hover:text-[#1e4fa3]' }}">Custom</button>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-[165px_165px_140px_minmax(0,1fr)]">
            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">From</span>
                <input wire:model.live="fromDate" type="date" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
            </label>
            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">To</span>
                <input wire:model.live="toDate" type="date" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
            </label>
            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</span>
                <select wire:model.live="statusFilter" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                    <option value="all">All Records</option>
                    <option value="complete">Complete Only</option>
                    <option value="incomplete">Incomplete Only</option>
                </select>
            </label>
            <label class="block">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Search</span>
                <input wire:model.live.debounce.300ms="search" type="search" placeholder="Name, code, email" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3] focus:bg-white">
            </label>
        </div>
        <div class="mt-4 flex justify-end">
            <a href="{{ $exportUrl }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-[#1e4fa3] px-5 py-2.5 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export CSV
            </a>
        </div>
    </section>

    @if($viewMode === 'table' && count($dailyTrend) > 0)
    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6" 
             x-data="attendanceChart" 
             data-labels="{{ json_encode(array_column($dailyTrend, 'label')) }}" 
             data-values="{{ json_encode(array_column($dailyTrend, 'hours')) }}" 
             wire:key="chart-section-{{ $fromDate }}-{{ $toDate }}">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-[#1e4fa3]">Daily Worked Hours Trend</h3>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ count($dailyTrend) }} days</p>
        </div>
        <div class="h-64 w-full">
            <canvas x-ref="canvas"></canvas>
        </div>
    </section>
    @endif

    @if($viewMode === 'table')
    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-white/80 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Student</th>
                            <th class="px-4 py-4">Time In</th>
                            <th class="px-4 py-4">Lunch Out</th>
                            <th class="px-4 py-4">Lunch In</th>
                            <th class="px-4 py-4">Time Out</th>
                            <th class="px-4 py-4">Status</th>
                            <th class="px-4 py-4">Worked</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-600">
                        @forelse($rows as $row)
                        <tr wire:key="attendance-report-row-{{ $row['id'] }}" class="transition hover:bg-slate-50">
                            <td class="px-4 py-4 align-top">{{ $row['date'] }}</td>
                            <td class="px-4 py-4 align-top">
                                <p class="font-semibold text-[#1e4fa3]">{{ $row['name'] }}</p>
                                <p class="mt-1 text-slate-500">{{ $row['email'] }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $row['student_code'] ?: 'No student code' }}</p>
                            </td>
                            <td class="px-4 py-4 align-top">{{ $row['time_in'] ?: '—' }}</td>
                            <td class="px-4 py-4 align-top">{{ $row['lunch_out'] ?: '—' }}</td>
                            <td class="px-4 py-4 align-top">{{ $row['lunch_in'] ?: '—' }}</td>
                            <td class="px-4 py-4 align-top">{{ $row['time_out'] ?: '—' }}</td>
                            <td class="px-4 py-4 align-top">
                                @if($row['time_out'])
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Complete
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    In Progress
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top font-semibold text-[#1e4fa3]">{{ $row['worked_hours'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="rounded-full bg-slate-100 p-4">
                                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500">No records matched the current filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 space-y-4 lg:hidden">
            @forelse($rows as $row)
            <article wire:key="attendance-report-card-{{ $row['id'] }}" class="rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-4 shadow-[0_20px_45px_-35px_rgba(15,23,42,0.35)]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $row['date'] }}</p>
                        <p class="mt-1 text-base font-bold text-[#1e4fa3]">{{ $row['name'] }}</p>
                    </div>
                    @if($row['time_out'])
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Complete</span>
                    @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">In Progress</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-slate-500">{{ $row['email'] }}</p>
                <p class="text-xs text-slate-400">{{ $row['student_code'] ?: 'No student code' }}</p>
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm text-slate-600">
                    <div class="rounded-xl bg-slate-50 p-2">
                        <span class="text-xs text-slate-400">In</span>
                        <p class="font-medium">{{ $row['time_in'] ?: '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-2">
                        <span class="text-xs text-slate-400">Out</span>
                        <p class="font-medium">{{ $row['time_out'] ?: '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-2">
                        <span class="text-xs text-slate-400">Lunch Out</span>
                        <p class="font-medium">{{ $row['lunch_out'] ?: '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-2">
                        <span class="text-xs text-slate-400">Lunch In</span>
                        <p class="font-medium">{{ $row['lunch_in'] ?: '—' }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-xs text-slate-500">Total Worked</span>
                    <span class="rounded-full bg-[#e8f0ff] px-3 py-1 text-sm font-bold text-[#1e4fa3]">{{ $row['worked_hours'] }}</span>
                </div>
            </article>
            @empty
            <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-14 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="rounded-full bg-slate-100 p-4">
                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-slate-500">No records matched the current filters.</p>
                </div>
            </div>
            @endforelse
        </div>

        @if($paginator->hasPages())
        <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-center text-sm text-slate-500 sm:text-left">Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }} records.</p>
            {{ $paginator->links() }}
        </div>
        @endif
    </section>
    @endif

    @if($viewMode === 'calendar')
    <x-admin.layouts.attendance-calendar
        wire:key="calendar-section-{{ $fromDate }}-{{ $toDate }}"
        :from-date="$fromDate"
        :to-date="$toDate"
        :current-month="$currentMonth"
        :total-hours="$periodTotals['totalHours']"
        :calendar-days="$calendarDays"
    />
    @endif
</div>