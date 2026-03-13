<?php

namespace App\Livewire\Admin;

use App\Models\DailyTimeRecord;
use App\Support\AttendanceMetrics;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Phase 5: Today's Attendance Reactive Table Component
 * Displays real-time attendance status for the current day with filtering and pagination.
 * Shows summary cards (total, completed, in-progress) and a paginated table/card view.
 */
class TodayAttendanceTable extends Component
{
    use WithPagination;

    // Records per page for paginated table view
    private const PER_PAGE = 10;

    // Search term for filtering by name, code, or email
    public string $search = '';

    // Completion filter: 'all', 'complete', or 'incomplete'
    public string $completionFilter = 'all';

    // Persist filter state in URL query string for bookmarkable reports
    protected $queryString = [
        'search' => ['except' => ''],
        'completionFilter' => ['except' => 'all'],
    ];

    /**
     * Reset pagination when search term changes to show results from page 1.
     * Fired when wire:model.live detects a search input change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when completion filter changes to show filtered results from page 1.
     * Fired when wire:model.live detects a filter selection change.
     */
    public function updatedCompletionFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Render the today attendance table with summary cards and paginated records.
     * Injected AttendanceMetrics dependency for time calculations.
     */
    public function render(AttendanceMetrics $metrics)
    {
        // Get today's date in YYYY-MM-DD format
        $today = now()->toDateString();

        // Build base query: join attendance records with student profiles
        $query = DailyTimeRecord::query()
            ->join('users', 'users.id', '=', 'daily_time_records.user_id')
            // Filter to today's date only
            ->whereDate('daily_time_records.date', $today)
            // Only include student records
            ->where('users.role', 'student')
            // Apply completion filter: complete (time_out set) vs incomplete (time_out null)
            ->when($this->completionFilter !== 'all', function ($query): void {
                if ($this->completionFilter === 'complete') {
                    // "Complete" records have a time_out value
                    $query->whereNotNull('daily_time_records.time_out');

                    return;
                }

                // "Incomplete" records are missing time_out
                $query->whereNull('daily_time_records.time_out');
            })
            // Apply search filter: name, code, or email
            ->when($this->search !== '', function ($query): void {
                $term = '%' . $this->search . '%';

                $query->where(function ($nested) use ($term): void {
                    // Multi-field search for flexible lookup
                    $nested
                        ->where('users.first_name', 'like', $term)
                        ->orWhere('users.middle_name', 'like', $term)
                        ->orWhere('users.last_name', 'like', $term)
                        ->orWhere('users.email', 'like', $term)
                        ->orWhere('users.student_code', 'like', $term);
                });
            })
            // Order by most recently updated records first (active students at top)
            ->orderByDesc('daily_time_records.updated_at');

        // Paginate results: 10 records per page, select only needed columns
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

        // Transform each record: build display data with computed worked hours
        $rows = $records->through(function ($record) use ($metrics): array {
            $data = $record->toArray();

            // Combine name parts, filter out nulls, and trim
            $name = trim(collect([
                $data['first_name'] ?? null,
                $data['middle_name'] ?? null,
                $data['last_name'] ?? null,
            ])->filter()->implode(' '));

            // Calculate net worked seconds using AttendanceMetrics helper
            $workedSeconds = $metrics->workedSeconds($data);

            // Return formatted row data for template
            return [
                'id' => $data['id'],
                'name' => $name !== '' ? $name : 'Student Profile Incomplete',
                'email' => $data['email'] ?? '',
                'student_code' => $data['student_code'] ?? null,
                'time_in' => $data['time_in'] ?? null,
                'lunch_out' => $data['lunch_out'] ?? null,
                'lunch_in' => $data['lunch_in'] ?? null,
                'time_out' => $data['time_out'] ?? null,
                // Format seconds as HH:MM
                'worked_hours' => $metrics->formatHoursMinutes($workedSeconds),
                // Status: record is complete if time_out is set
                'is_complete' => ! empty($data['time_out']),
            ];
        });

        // Build summary stats: total records, completed (time_out set), in-progress (time_in set but no time_out)
        $summary = [
            'total' => DailyTimeRecord::query()->whereDate('date', $today)->count(),
            'completed' => DailyTimeRecord::query()->whereDate('date', $today)->whereNotNull('time_out')->count(),
            'inProgress' => DailyTimeRecord::query()->whereDate('date', $today)->whereNotNull('time_in')->whereNull('time_out')->count(),
        ];

        // Pass all data to Blade template for rendering
        return view('livewire.admin.today-attendance-table', [
            'records' => $rows,
            'paginator' => $records,
            'summary' => $summary,
        ]);
    }
}