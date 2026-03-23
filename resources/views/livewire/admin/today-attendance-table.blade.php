<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <section class="rounded-[1.6rem] border border-[#d7e2f5] bg-[linear-gradient(135deg,rgba(255,255,255,0.96),rgba(224,235,247,0.96))] p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Today's Records</p>
            <p class="mt-3 text-4xl font-black text-[#1e4fa3]">{{ $summary['total'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Students who have at least one attendance entry today.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Completed</p>
            <p class="mt-3 text-4xl font-black text-emerald-700">{{ $summary['completed'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Students who already reached time-out.</p>
        </section>

        <section class="rounded-[1.6rem] border border-slate-200 bg-white p-5 shadow-[0_28px_60px_-38px_rgba(15,23,42,0.35)]">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">In Progress</p>
            <p class="mt-3 text-4xl font-black text-amber-700">{{ $summary['inProgress'] }}</p>
            <p class="mt-2 text-sm text-slate-600">Students with a started day but no time-out yet.</p>
        </section>
    </div>

    <section class="rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6">
        <div class="flex flex-col gap-4 border-b border-[#f1c74a] pb-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1e4fa3] sm:text-2xl">Today&apos;s Attendance</h2>
                <p class="mt-1 text-sm text-slate-500">Track currently active students and completed daily logs in real time.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[220px_minmax(0,320px)]">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Completion</span>
                    <select wire:model.live="completionFilter" class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm font-semibold text-[#1e4fa3] outline-none transition focus:border-[#1e4fa3] focus:bg-white">
                        <option value="all">All</option>
                        <option value="complete">Complete</option>
                        <option value="incomplete">In Progress</option>
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Search attendance</span>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Search</span>
                    <input wire:model.live.debounce.300ms="search" type="search" placeholder="Name, code, email"
                        class="w-full rounded-2xl border border-[#d5e0f0] bg-[#f7f9fc] px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#1e4fa3] focus:bg-white">
                </label>
            </div>
        </div>

        <div class="mt-6 space-y-4 lg:hidden">
            @forelse($records as $row)
                <article wire:key="today-attendance-card-{{ $row['id'] }}" class="rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] p-4 shadow-[0_20px_45px_-35px_rgba(15,23,42,0.35)]">
                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="text-base font-bold text-[#1e4fa3]">{{ $row['name'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $row['email'] }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $row['student_code'] ?: 'No student code yet' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-500">In:</span> {{ $row['time_in'] ?: '—' }}</p>
                            <p><span class="font-semibold text-slate-500">Out:</span> {{ $row['time_out'] ?: '—' }}</p>
                            <p><span class="font-semibold text-slate-500">Lunch Out:</span> {{ $row['lunch_out'] ?: '—' }}</p>
                            <p><span class="font-semibold text-slate-500">Lunch In:</span> {{ $row['lunch_in'] ?: '—' }}</p>
                        </div>

                        <div class="text-xs text-slate-500">
                            <p class="font-semibold text-slate-500">Location:</p>
                            @if($row['time_in_latitude'] && $row['time_in_longitude'])
                                <p>In: {{ number_format($row['time_in_latitude'], 6) }}, {{ number_format($row['time_in_longitude'], 6) }}</p>
                            @endif
                            @if($row['time_out_latitude'] && $row['time_out_longitude'])
                                <p class="mt-0.5">Out: {{ number_format($row['time_out_latitude'], 6) }}, {{ number_format($row['time_out_longitude'], 6) }}</p>
                            @endif
                            @if((!$row['time_in_latitude'] || !$row['time_in_longitude']) && (!$row['time_out_latitude'] || !$row['time_out_longitude']))
                                <p>—</p>
                            @endif
                        </div>

                        <div class="flex items-center">
                            <span class="rounded-full bg-[#e8f0ff] px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#1e4fa3]">{{ $row['worked_hours'] }} hrs</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[1.5rem] border border-dashed border-[#d5e0f0] bg-[#f7f9fc] px-6 py-14 text-center text-sm text-slate-500">No attendance records matched this view.</div>
            @endforelse
        </div>

        <div class="mt-6 hidden overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[#fbfbfc] lg:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-white/80 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        <tr>
                            <th class="px-4 py-4">Student</th>
                            <th class="px-4 py-4">Time In</th>
                            <th class="px-4 py-4">Lunch Out</th>
                            <th class="px-4 py-4">Lunch In</th>
                            <th class="px-4 py-4">Time Out</th>
                            <th class="px-4 py-4">Location</th>
                            <th class="px-4 py-4">Worked</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-600">
                        @forelse($records as $row)
                            <tr wire:key="today-attendance-row-{{ $row['id'] }}">
                                <td class="px-4 py-4 align-top">
                                    <p class="font-semibold text-[#1e4fa3]">{{ $row['name'] }}</p>
                                    <p class="mt-1 text-slate-500">{{ $row['email'] }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $row['student_code'] ?: 'No student code yet' }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">{{ $row['time_in'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top">{{ $row['lunch_out'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top">{{ $row['lunch_in'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top">{{ $row['time_out'] ?: '—' }}</td>
                                <td class="px-4 py-4 align-top text-xs text-slate-500">
                                    @if($row['time_in_latitude'] && $row['time_in_longitude'])
                                        <span class="block">In: {{ number_format($row['time_in_latitude'], 6) }}, {{ number_format($row['time_in_longitude'], 6) }}</span>
                                    @endif
                                    @if($row['time_out_latitude'] && $row['time_out_longitude'])
                                        <span class="block mt-1">Out: {{ number_format($row['time_out_latitude'], 6) }}, {{ number_format($row['time_out_longitude'], 6) }}</span>
                                    @endif
                                    @if((!$row['time_in_latitude'] || !$row['time_in_longitude']) && (!$row['time_out_latitude'] || !$row['time_out_longitude']))
                                        <span>—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top font-semibold text-[#1e4fa3]">{{ $row['worked_hours'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center text-sm text-slate-500">No attendance records matched this view.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($paginator->hasPages())
            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-center text-sm text-slate-500 sm:text-left">Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }} records.</p>
                {{ $paginator->links() }}
            </div>
        @endif
    </section>
</div>