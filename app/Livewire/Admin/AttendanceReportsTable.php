<?php

namespace App\Livewire\Admin;

use App\Models\DailyTimeRecord;
use App\Support\AttendanceMetrics;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Phase 5: Attendance Reports Reactive Table Component
 * Displays period-based attendance data with date range filtering, daily trend chart, and paginated records.
 * Includes automatic date validation and CSV export integration.
 */
class AttendanceReportsTable extends Component
{
    use WithPagination;

    // Pagination limit
    private const PER_PAGE = 10;

    // View mode: 'table' or 'calendar'
    public string $viewMode = 'table';

    // Status filter: 'all', 'complete', 'incomplete'
    public string $statusFilter = 'all';

    // Active date preset: 'custom', 'today', 'week', 'month', 'last_month'
    public string $datePreset = 'month';

    // Search term for filtering by name, code, email
    public string $search = '';

    // From date for report period (updated by wire:model.live)
    public string $fromDate = '';

    // To date for report period (updated by wire:model.live)
    public string $toDate = '';

    /**
     * Initialize component with sensible defaults: current month's start to today.
     * Ensures first page load shows a meaningful date range (not empty).
     */
    public function mount(): void
    {
        // Set from date to first day of current month
        $this->fromDate = now()->startOfMonth()->toDateString();
        // Set to date to today
        $this->toDate = now()->toDateString();
        // Default to month preset
        $this->datePreset = 'month';
    }

    /**
     * Reset pagination when from date changes to show filtered results from page 1.
     */
    public function updatedFromDate(): void
    {
        $this->datePreset = 'custom';
        $this->resetPage();
    }

    /**
     * Reset pagination when to date changes.
     */
    public function updatedToDate(): void
    {
        $this->datePreset = 'custom';
        $this->resetPage();
    }

    /**
     * Reset pagination when search term changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Set view mode (table or calendar).
     */
    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    /**
     * Set date preset to custom.
     */
    public function setCustomPreset(): void
    {
        $this->datePreset = 'custom';
    }

    /**
     * Navigate to previous or next month in calendar view.
     */
    public function navigateMonth(string $direction): void
    {
        $from = Carbon::parse($this->fromDate);
        $to = Carbon::parse($this->toDate);

        if ($direction === 'previous') {
            $this->fromDate = $from->subMonth()->startOfMonth()->toDateString();
            $this->toDate = $from->subMonth()->endOfMonth()->toDateString();
        } else {
            $this->fromDate = $from->addMonth()->startOfMonth()->toDateString();
            $this->toDate = $from->addMonth()->endOfMonth()->toDateString();
        }

        $this->datePreset = 'custom';
        $this->resetPage();
    }

    /**
     * Apply date preset and update date range.
     */
    public function applyDatePreset(string $preset): void
    {
        $this->datePreset = $preset;

        switch ($preset) {
            case 'today':
                $this->fromDate = now()->toDateString();
                $this->toDate = now()->toDateString();
                break;
            case 'week':
                $this->fromDate = now()->startOfWeek()->toDateString();
                $this->toDate = now()->endOfWeek()->toDateString();
                break;
            case 'month':
                $this->fromDate = now()->startOfMonth()->toDateString();
                $this->toDate = now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->fromDate = now()->subMonth()->startOfMonth()->toDateString();
                $this->toDate = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'custom':
                // Keep current dates, let user adjust manually
                break;
        }

        $this->resetPage();
        $this->dispatch('chartUpdated');
    }

    /**
     * Build the CSV export URL with current filters (from, to, search).
     * Used by the "Export CSV" button in the template.
     *
     * @return string Route URL with query parameters for export controller
     */
    public function exportUrl(): string
    {
        return route('admin.attendance.reports.export', [
            'from' => $this->normalizedFromDate(),
            'to' => $this->normalizedToDate(),
            'search' => $this->search !== '' ? $this->search : null,
        ]);
    }

    /**
     * Render the reports page with filtered records, daily trend chart, and period statistics.
     */
    public function render(AttendanceMetrics $metrics)
    {
        // Get validated date range (handles invalid/inverted dates automatically)
        $from = $this->normalizedFromDate();
        $to = $this->normalizedToDate();

        // Build base query: join attendance with student profiles, filter by date range and status
        $query = DailyTimeRecord::query()
            ->join('users', 'users.id', '=', 'daily_time_records.user_id')
            // Date range boundaries (inclusive)
            ->whereDate('daily_time_records.date', '>=', $from)
            ->whereDate('daily_time_records.date', '<=', $to)
            // Students only
            ->where('users.role', 'student')
            // Apply status filter
            ->when($this->statusFilter === 'complete', function ($query): void {
                $query->whereNotNull('daily_time_records.time_out');
            })
            ->when($this->statusFilter === 'incomplete', function ($query): void {
                $query->whereNull('daily_time_records.time_out');
            })
            // Apply search filter for multi-field lookup
            ->when($this->search !== '', function ($query): void {
                $term = '%' . $this->search . '%';

                $query->where(function ($nested) use ($term): void {
                    $nested
                        ->where('users.first_name', 'like', $term)
                        ->orWhere('users.middle_name', 'like', $term)
                        ->orWhere('users.last_name', 'like', $term)
                        ->orWhere('users.email', 'like', $term)
                        ->orWhere('users.student_code', 'like', $term);
                });
            })
            // Order: most recent dates first, then by student last name
            ->orderByDesc('daily_time_records.date')
            ->orderBy('users.last_name');

        // Paginate: 10 records per page, select only needed columns
        $records = $query->paginate(self::PER_PAGE, [
            'daily_time_records.id',
            'daily_time_records.date',
            'daily_time_records.time_in',
            'daily_time_records.lunch_out',
            'daily_time_records.lunch_in',
            'daily_time_records.time_out',
            'users.student_code',
            'users.first_name',
            'users.middle_name',
            'users.last_name',
            'users.email',
        ]);

        // Transform records: build display data with formatted dates and worked hours
        $rows = $records->through(function ($record) use ($metrics): array {
            $data = $record->toArray();

            $name = trim(collect([
                $data['first_name'] ?? null,
                $data['middle_name'] ?? null,
                $data['last_name'] ?? null,
            ])->filter()->implode(' '));

            return [
                'id' => $data['id'],
                'date' => Carbon::parse((string) ($data['date'] ?? now()->toDateString()))->format('M j, Y'),
                'name' => $name !== '' ? $name : 'Student Profile Incomplete',
                'student_code' => $data['student_code'] ?? null,
                'email' => $data['email'] ?? '',
                'time_in' => $data['time_in'] ?? null,
                'lunch_out' => $data['lunch_out'] ?? null,
                'lunch_in' => $data['lunch_in'] ?? null,
                'time_out' => $data['time_out'] ?? null,
                'worked_hours' => $metrics->formatHoursMinutes($metrics->workedSeconds($data)),
            ];
        });

        // Fetch records for chart: group by date and sum worked hours per day
        $chartRows = DailyTimeRecord::query()
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->get(['date', 'time_in', 'lunch_out', 'lunch_in', 'time_out'])
            // Group by date string for efficient daily aggregation
            ->groupBy(fn ($record) => Carbon::parse((string) $record->date)->toDateString());

        // Build daily trend: one point per day in the selected range, with aggregated worked hours
        $dailyTrend = collect(CarbonPeriod::create($from, $to))
            ->map(function (Carbon $day) use ($chartRows, $metrics): array {
                // Get date key in YYYY-MM-DD format
                $key = $day->toDateString();
                // Retrieve all records for this date (null if none exist)
                $entries = $chartRows->get($key, collect());

                // Sum worked seconds for all entries on this day
                $seconds = $entries
                    ->map(fn ($record): int => $metrics->workedSeconds($record->toArray()))
                    ->sum();

                // Return chart point: date label and hours
                return [
                    'label' => $day->format('M j'),
                    'hours' => round($seconds / 3600, 2),
                ];
            })
            ->all();

        // Calculate period totals: record count, completed (time_out set), and total hours
        $periodTotals = [
            'records' => DailyTimeRecord::query()->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->count(),
            'completed' => DailyTimeRecord::query()->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->whereNotNull('time_out')->count(),
            'totalHours' => round(array_sum(array_column($dailyTrend, 'hours')), 2),
        ];

        // Build flat calendar days array for Alpine component (not grouped by month)
        $calendarDays = collect(CarbonPeriod::create($from, $to))
            ->map(function (Carbon $day, $index) use ($dailyTrend) {
                $dateKey = $day->toDateString();
                $recordsForDay = DailyTimeRecord::query()
                    ->join('users', 'users.id', '=', 'daily_time_records.user_id')
                    ->whereDate('daily_time_records.date', $dateKey)
                    ->where('users.role', 'student')
                    ->when($this->statusFilter === 'complete', function ($q) {
                        $q->whereNotNull('daily_time_records.time_out');
                    })
                    ->when($this->statusFilter === 'incomplete', function ($q) {
                        $q->whereNull('daily_time_records.time_out');
                    })
                    ->get([
                        'daily_time_records.*',
                        'users.first_name',
                        'users.last_name',
                        'users.student_code',
                        'users.email',
                    ]);

                // Find hours for this day from dailyTrend
                $dayHours = 0;
                foreach ($dailyTrend as $trend) {
                    if ($trend['label'] === $day->format('M j')) {
                        $dayHours = $trend['hours'];
                        break;
                    }
                }

                return [
                    'date' => $dateKey,
                    'day' => $day->day,
                    'month' => $day->format('M'),
                    'year' => $day->year,
                    'weekday' => $day->format('D'),
                    'isToday' => $day->isToday(),
                    'hasRecords' => $recordsForDay->count() > 0,
                    'recordCount' => $recordsForDay->count(),
                    'hours' => $dayHours,
                    'records' => $recordsForDay->map(function ($record) {
                        $name = trim(($record->first_name ?? '') . ' ' . ($record->last_name ?? ''));

                        // Joined columns are commonly returned as strings (not Carbon instances)
                        $timeIn = filled($record->time_in) ? substr((string) $record->time_in, 0, 5) : null;
                        $timeOut = filled($record->time_out) ? substr((string) $record->time_out, 0, 5) : null;

                        return [
                            'id' => $record->id,
                            'name' => $name !== '' ? $name : null,
                            'code' => $record->student_code,
                            'timeIn' => $timeIn,
                            'timeOut' => $timeOut,
                        ];
                    })->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();

        // Find maximum hours in trend for chart bar scaling (prevent division by zero)
        $maxHours = max(1.0, (float) max(array_column($dailyTrend, 'hours') ?: [1.0]));

        // Dispatch chart data update for Alpine.js to receive
        $this->dispatch('chartDataUpdated', [
            'labels' => array_column($dailyTrend, 'label'),
            'data' => array_column($dailyTrend, 'hours'),
        ]);

        // Current month label for calendar header
        $currentMonth = Carbon::parse($from)->format('F Y');

        return view('livewire.admin.attendance-reports-table', [
            'rows' => $rows,
            'paginator' => $records,
            'dailyTrend' => $dailyTrend,
            'periodTotals' => $periodTotals,
            'maxHours' => $maxHours,
            'calendarDays' => $calendarDays,
            'currentMonth' => $currentMonth,
            'fromDate' => $from,
            'toDate' => $to,
            'exportUrl' => $this->exportUrl(),
        ]);
    }

    /**
     * Validate and return normalized from date.
     * Handles: invalid date strings, inverted date ranges, and empty values.
     * Returns sensible default (start of month) if invalid or inverted.
     *
     * @return string From date in YYYY-MM-DD format
     */
    private function normalizedFromDate(): string
    {
        // Parse both dates with fallbacks
        $from = $this->safeDate($this->fromDate, now()->startOfMonth());
        $to = $this->safeDate($this->toDate, now());

        // If from > to (inverted), return to date instead to prevent invalid range
        return $from->greaterThan($to) ? $to->copy()->startOfDay()->toDateString() : $from->toDateString();
    }

    /**
     * Validate and return normalized to date.
     * Handles: invalid date strings, inverted date ranges, and empty values.
     * Returns sensible default (today) if invalid or inverted.
     *
     * @return string To date in YYYY-MM-DD format
     */
    private function normalizedToDate(): string
    {
        // Parse both dates with fallbacks
        $from = $this->safeDate($this->fromDate, now()->startOfMonth());
        $to = $this->safeDate($this->toDate, now());

        // If to < from (inverted), return from date instead to prevent invalid range
        return $to->lessThan($from) ? $from->copy()->toDateString() : $to->toDateString();
    }

    /**
     * Safely parse a date string, returning fallback on parse failure.
     * Handles: empty strings, malformed dates, null values gracefully.
     *
     * @param string $value Raw date string (from input field)
     * @param Carbon $fallback Date to use if parsing fails
     * @return Carbon Parsed date or fallback
     */
    private function safeDate(string $value, Carbon $fallback): Carbon
    {
        try {
            // Attempt to parse the date string
            return Carbon::parse($value);
        } catch (\Throwable) {
            // On any parse error, return the fallback date
            return $fallback->copy();
        }
    }
}