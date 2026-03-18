<div class="space-y-6">

    {{-- Main Reports Section with Filters, Chart, and Records Table --}}
    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        {{-- Section Header and Filter Controls --}}
        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Attendance Reports</h2>
                <p class="mt-1 text-sm text-slate-500">Filter by date range and search keywords, then export to CSV for external reporting.</p>
            </div>

            {{-- Filters: From Date, To Date, and Search Input --}}
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[165px_165px_minmax(0,260px)]">
                {{-- From Date Filter --}}
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">From</span>
                    <input wire:model.live="fromDate" type="date" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                </label>

                {{-- To Date Filter --}}
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">To</span>
                    <input wire:model.live="toDate" type="date" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                </label>

                {{-- Search Input: Name, Code, or Email --}}
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Search</span>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Name, code, email"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3] focus:bg-white">
                </label>
            </div>
        </div>

        {{-- Export CSV Button --}}
        <div class="mt-4 flex justify-end">
            <a href="{{ $exportUrl }}" class="inline-flex items-center justify-center rounded-full bg-[#1e4fa3] px-4 py-2 text-sm font-semibold text-white shadow-[0_18px_40px_-24px_rgba(30,79,163,0.8)] transition hover:bg-[#173d79]">
                Export CSV
            </a>
        </div>

        {{-- Daily Trend Chart Section --}}
        <div class="mt-6 rounded-[1.4rem] border border-slate-200 bg-[#fbfbfc] p-4 sm:p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h3 class="text-base font-bold text-[#1e4fa3]">Daily Worked Hours Trend</h3>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Chart / Statistics</p>
            </div>

            {{-- Chart bars: each bar represents one day's aggregated worked hours --}}
            <div class="space-y-3">
                @forelse($dailyTrend as $index => $point)
                    {{-- Calculate normalized bar width (0-100%) for consistent scaling across date ranges --}}
                    @php
                        $barWidth = min(100, max(0, ($point['hours'] / $maxHours) * 100));
                    @endphp
                    <div wire:key="trend-point-{{ $index }}" class="grid grid-cols-[72px_minmax(0,1fr)_62px] items-center gap-3 text-sm">
                        {{-- Date Label Column --}}
                        <span class="font-semibold text-slate-500">{{ $point['label'] }}</span>
                        {{-- Bar Container and Dynamic Bar (using Alpine.js for width binding) --}}
                        <div class="h-2.5 rounded-full bg-slate-200">
                            <div
                                x-data="{ width: @js($barWidth) }"
                                x-bind:style="`width: ${width}%`"
                                class="h-2.5 rounded-full bg-[#1e4fa3] transition-all duration-300"
                            ></div>
                        </div>
                        {{-- Hours Value Column --}}
                        <span class="text-right font-semibold text-[#1e4fa3]">{{ number_format($point['hours'], 2) }}</span>
                    </div>
                @empty
                    {{-- Empty State: no data for selected date range --}}
                    <p class="text-sm text-slate-500">No attendance points were found for this date range.</p>
                @endforelse
            </div>
        </div>

        {{-- Desktop Table View (reports records) --}}
        <div class="mt-6 hidden overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    {{-- Table Header Row --}}
                    <thead class="bg-white/80 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Student</th>
                            <th class="px-4 py-4">Time In</th>
                            <th class="px-4 py-4">Lunch Out</th>
                            <th class="px-4 py-4">Lunch In</th>
                            <th class="px-4 py-4">Time Out</th>
                            <th class="px-4 py-4">Worked</th>
                        </tr>
                    </thead>
                    {{-- Table Body: Attendance Records with Formatting --}}
                    <tbody class="divide-y divide-slate-200 text-slate-600">
                        @forelse($rows as $row)
                            <tr wire:key="attendance-report-row-{{ $row['id'] }}">
                                {{-- Date Cell: formatted as "M j, Y" (e.g., "Mar 10, 2026") --}}
                                <td class="px-4 py-4 align-top">{{ $row['date'] }}</td>
                                {{-- Student Profile Cell: name, email, code --}}
                                <td class="px-4 py-4 align-top">
                                    <p class="font-semibold text-[#1e4fa3]">{{ $row['name'] }}</p>
                                    <p class="mt-1 text-slate-500">{{ $row['email'] }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $row['student_code'] ?: 'No student code yet' }}</p>
                                </td>
                                {{-- Time Cells: display time or \"—\" if null --}}
                                <td class="px-4 py-4 align-top">{{ $row['time_in'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top">{{ $row['lunch_out'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top">{{ $row['lunch_in'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top">{{ $row['time_out'] ?: '—' }}</td>
                                {{-- Worked Hours Cell --}}
                                <td class="px-4 py-4 align-top font-semibold text-[#1e4fa3]">{{ $row['worked_hours'] }}</td>
                            </tr>
                        @empty
                            {{-- Empty State: no records match filters --}}
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center text-sm text-slate-500">No records matched the current report filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Card View (reports records as cards) --}}
        <div class="mt-6 space-y-4 lg:hidden">
            @forelse($rows as $row)
                {{-- Each attendance record displayed as a card on mobile --}}
                <article wire:key="attendance-report-card-{{ $row['id'] }}" class="rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-4 shadow-[0_20px_45px_-35px_rgba(15,23,42,0.35)]">
                    {{-- Date Badge --}}
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $row['date'] }}</p>
                    {{-- Student Info --}}
                    <p class="mt-2 text-base font-bold text-[#1e4fa3]">{{ $row['name'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $row['email'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $row['student_code'] ?: 'No student code yet' }}</p>

                    {{-- Time Details Grid --}}
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-500">In:</span> {{ $row['time_in'] ?: '—' }}</p>
                        <p><span class="font-semibold text-slate-500">Out:</span> {{ $row['time_out'] ?: '—' }}</p>
                        <p><span class="font-semibold text-slate-500">Lunch Out:</span> {{ $row['lunch_out'] ?: '—' }}</p>
                        <p><span class="font-semibold text-slate-500">Lunch In:</span> {{ $row['lunch_in'] ?: '—' }}</p>
                    </div>

                    {{-- Worked Hours Badge --}}
                    <p class="mt-4 inline-flex rounded-full bg-[#e8f0ff] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#1e4fa3]">{{ $row['worked_hours'] }} hrs</p>
                </article>
            @empty
                {{-- Empty State: no records match filters --}}
                <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-14 text-center text-sm text-slate-500">No records matched the current report filters.</div>
            @endforelse
        </div>

        {{-- Pagination Controls (only show if table has multiple pages) --}}
        @if($paginator->hasPages())
            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-center text-sm text-slate-500 sm:text-left">Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }} records.</p>
                {{ $paginator->links() }}
            </div>
        @endif
    </section>
</div>