@props([
    'fromDate',
    'toDate',
    'currentMonth',
    'totalHours',
    'calendarDays',
])

<section {{ $attributes->merge(['class' => 'rounded-[1.8rem] border border-slate-200/80 bg-white/90 p-5 shadow-[0_30px_70px_-42px_rgba(15,23,42,0.45)] backdrop-blur sm:p-6']) }}
    x-data="attendanceCalendar"
    data-from-date="{{ $fromDate }}"
    data-to-date="{{ $toDate }}"
    data-current-month="{{ $currentMonth }}"
    data-total-hours="{{ $totalHours }}">

    <script type="application/json" x-ref="calendarDaysJson">@json($calendarDays)</script>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button @click="previousMonth" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-600 transition hover:border-[#1e4fa3] hover:text-[#1e4fa3]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h3 class="text-base font-bold text-[#1e4fa3]" x-text="currentMonth"></h3>
            <button @click="nextMonth" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-600 transition hover:border-[#1e4fa3] hover:text-[#1e4fa3]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
        <p class="text-xs text-slate-500" x-text="totalHours"></p>
    </div>

    <div class="grid grid-cols-7 gap-2 mb-2">
        <template x-for="day in dayNames" :key="day">
            <div class="py-2 text-center text-xs font-bold uppercase tracking-wider text-slate-400" x-text="day"></div>
        </template>
    </div>

    <div class="grid grid-cols-7 gap-2">
        <template x-for="(day, index) in calendarGrid" :key="index">
            <div class="aspect-square rounded-xl border p-2 transition cursor-pointer"
                :class="{
                    'border-slate-200 bg-white': !day.isEmpty,
                    'border-slate-100 bg-slate-50/50': day.isEmpty,
                    'ring-2 ring-[#1e4fa3] ring-offset-2': day.isToday,
                    'hover:border-[#1e4fa3] hover:shadow-md': day.hasRecords && !day.isEmpty
                }"
                @click="openDayModal(day)">
                <div class="flex h-full flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <span class="text-sm font-semibold"
                            :class="day.isEmpty ? 'text-slate-300' : 'text-slate-700'"
                            x-text="day.day"></span>
                        <span x-show="day.hasRecords"
                            class="flex h-5 w-5 items-center justify-center rounded-full bg-[#1e4fa3] text-[10px] font-bold text-white"
                            x-text="day.recordCount"></span>
                    </div>
                    <template x-if="day.hasRecords && day.hours > 0">
                        <div class="mt-1 text-xs font-semibold text-[#1e4fa3]" x-text="day.hours.toFixed(1) + 'h'"></div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-slate-500">
        <div class="flex items-center gap-2">
            <div class="h-4 w-4 rounded border border-[#1e4fa3] bg-[#1e4fa3]"></div>
            <span>Has Records</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-4 w-4 rounded border border-slate-200 bg-white ring-2 ring-[#1e4fa3]"></div>
            <span>Today</span>
        </div>
    </div>

    <div x-show="showDayModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="closeDayModal">
        <div x-show="showDayModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <template x-if="selectedDay">
                <div>
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <h4 class="text-lg font-bold text-[#1e4fa3]" x-text="formatDate(selectedDay.date)"></h4>
                            <p class="text-sm text-slate-500">
                                <span x-text="selectedDay.recordCount"></span> records ·
                                <span x-text="selectedDay.hours.toFixed(1)"></span> hours
                            </p>
                        </div>
                        <button @click="closeDayModal" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="max-h-64 space-y-2 overflow-y-auto">
                        <template x-for="record in selectedDay.records" :key="record.id">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-700" x-text="record.name"></p>
                                        <p class="text-xs text-slate-400" x-text="record.code"></p>
                                    </div>
                                    <div class="text-right text-xs">
                                        <p class="text-slate-600">
                                            <span x-text="record.timeIn || '--:--'"></span> →
                                            <span x-text="record.timeOut || '--:--'"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>
