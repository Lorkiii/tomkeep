<?php

namespace App\Support;

use App\Models\DailyTimeRecord;
use App\Models\User;
use Carbon\Carbon;

/**
 * Derives dashboard metrics from daily time records.
 *
 * This class exists so route closures and Blade files don't need to perform
 * reporting logic, date grouping, or log formatting decisions.
 */
class UserDashboardStats
{
    /**
     * Build the dashboard summary data for one student.
     *
     * Returned values are already shaped for the dashboard UI.
     *
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        // We compare all records against the same "now" value.
        $today = now();

        // Load attendance data once, then compute everything in memory.
        $records = DailyTimeRecord::query()
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->get(['date', 'time_in', 'lunch_out', 'lunch_in', 'time_out']);

        // Accumulators for progress and summary cards.
        $completedSeconds = 0;
        $todaySeconds = 0;
        $weekSeconds = 0;
        $monthSeconds = 0;

        // This powers the "Recent Logs" card on the home dashboard.
        $activityLogs = [];

        foreach ($records as $record) {
            // Normalise the date into a Carbon instance for easy comparison.
            $recordDate = $record->date instanceof Carbon
                ? $record->date->copy()
                : Carbon::parse((string) $record->date);

            // Net worked seconds are computed from time in/out minus lunch.
            $duration = $this->workedSeconds($record);
            $completedSeconds += $duration;

            // Dashboard stat: hours worked today.
            if ($recordDate->isSameDay($today)) {
                $todaySeconds += $duration;
            }

            // Dashboard stat: hours worked this ISO week.
            if ($recordDate->format('o-W') === $today->format('o-W')) {
                $weekSeconds += $duration;
            }

            // Dashboard stat: hours worked this calendar month.
            if ($recordDate->format('Y-m') === $today->format('Y-m')) {
                $monthSeconds += $duration;
            }

            // Recent logs intentionally show today's entries only.
            if (! $recordDate->isSameDay($today)) {
                continue;
            }

            // Push every attendance event so the user sees the full daily flow.
            $this->appendLog($activityLogs, $recordDate, $record->time_in, 'time_in', 'Time-In Work on Office');
            $this->appendLog($activityLogs, $recordDate, $record->lunch_out, 'lunch_out', 'Lunch-Out');
            $this->appendLog($activityLogs, $recordDate, $record->lunch_in, 'lunch_in', 'Lunch-In');
            $this->appendLog($activityLogs, $recordDate, $record->time_out, 'time_out', 'Time-Out Work on Office');
        }

        // Show newest log entries first.
        usort($activityLogs, static fn (array $a, array $b): int => strcmp((string) ($b['at'] ?? ''), (string) ($a['at'] ?? '')));

        // Keep the dashboard concise even if a future data shape adds more events.
        $activityLogs = array_slice($activityLogs, 0, 12);

        // Progress calculations are kept intentionally simple for readability.
        $requiredHours = (int) $user->number_of_hours;
        $completedHours = (int) floor($completedSeconds / 3600);
        $remainingHours = max(0, $requiredHours - $completedHours);
        $progressPercent = $requiredHours > 0
            ? min(100, (int) round(($completedHours / $requiredHours) * 100))
            : 0;

        // Return a plain array so routes can spread it directly into the view.
        return [
            'progressPercent' => $progressPercent,
            'remainingHours' => $remainingHours,
            'requiredHours' => $requiredHours,
            'activityLogs' => $activityLogs,
            'hoursThisDay' => (int) floor($todaySeconds / 3600),
            'hoursThisWeek' => (int) floor($weekSeconds / 3600),
            'hoursThisMonth' => (int) floor($monthSeconds / 3600),
        ];
    }

    /**
     * Append one attendance log entry if a time value exists.
     *
     * This prevents repeated if-blocks in the main aggregation loop.
     *
     * @param array<int, array<string, string>> $activityLogs
     */
    private function appendLog(array &$activityLogs, Carbon $recordDate, ?string $time, string $type, string $label): void
    {
        // Skip empty timestamps because the action has not happened yet.
        if (! $time) {
            return;
        }

        // Store a simple UI-friendly payload.
        $activityLogs[] = [
            'type' => $type,
            'label' => $label,
            'at' => $recordDate->format('Y-m-d') . ' ' . $time,
        ];
    }

    /**
     * Compute net work seconds for one record.
     *
     * Rules:
     * - If either time_in or time_out is missing, count as zero.
     * - If time_out is not later than time_in, count as zero.
     * - If lunch_out and lunch_in both exist, subtract the lunch interval.
     */
    private function workedSeconds(object $record): int
    {
        // Incomplete records should not inflate worked-hour totals.
        if (! $record->time_in || ! $record->time_out) {
            return 0;
        }

        // Rebuild full datetime strings so Carbon can compare times safely.
        $date = $record->date instanceof Carbon
            ? $record->date->format('Y-m-d')
            : Carbon::parse((string) $record->date)->format('Y-m-d');

        $timeIn = Carbon::parse($date . ' ' . $record->time_in);
        $timeOut = Carbon::parse($date . ' ' . $record->time_out);

        // Defensive guard against invalid data ordering.
        if ($timeOut->lessThanOrEqualTo($timeIn)) {
            return 0;
        }

        // Start with the full interval from time in to time out.
        $total = $timeOut->diffInSeconds($timeIn);

        // Subtract lunch only when both boundaries exist and are valid.
        if ($record->lunch_out && $record->lunch_in) {
            $lunchOut = Carbon::parse($date . ' ' . $record->lunch_out);
            $lunchIn = Carbon::parse($date . ' ' . $record->lunch_in);

            if ($lunchIn->greaterThan($lunchOut)) {
                $total -= $lunchIn->diffInSeconds($lunchOut);
            }
        }

        // Never allow negative totals to leak into the UI.
        return max(0, $total);
    }
}
