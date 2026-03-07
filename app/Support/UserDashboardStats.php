<?php

namespace App\Support;

use App\Models\DailyTimeRecord;
use App\Models\User;
use Carbon\Carbon;

/**
 * Derives dashboard metrics from daily time records.
 */
class UserDashboardStats
{
    /**
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        $records = DailyTimeRecord::query()
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->get(['date', 'time_in', 'lunch_out', 'lunch_in', 'time_out']);

        $completedSeconds = 0;
        $todaySeconds = 0;
        $weekSeconds = 0;
        $monthSeconds = 0;
        $activityLogs = [];

        $today = now();

        foreach ($records as $record) {
            $recordDate = $record->date instanceof Carbon
                ? $record->date->copy()
                : Carbon::parse((string) $record->date);

            $duration = $this->workedSeconds($record);
            $completedSeconds += $duration;

            if ($recordDate->isSameDay($today)) {
                $todaySeconds += $duration;
            }

            if ($recordDate->format('o-W') === $today->format('o-W')) {
                $weekSeconds += $duration;
            }

            if ($recordDate->format('Y-m') === $today->format('Y-m')) {
                $monthSeconds += $duration;
            }

            if ($record->time_out) {
                $activityLogs[] = [
                    'type' => 'time_out',
                    'label' => 'Work on Office',
                    'at' => $recordDate->format('Y-m-d') . ' ' . $record->time_out,
                ];
            }

            if ($record->time_in) {
                $activityLogs[] = [
                    'type' => 'time_in',
                    'label' => 'Work on Office',
                    'at' => $recordDate->format('Y-m-d') . ' ' . $record->time_in,
                ];
            }
        }

        usort($activityLogs, static fn (array $a, array $b): int => strcmp((string) ($b['at'] ?? ''), (string) ($a['at'] ?? '')));
        $activityLogs = array_slice($activityLogs, 0, 20);

        $requiredHours = (int) $user->number_of_hours;
        $completedHours = (int) floor($completedSeconds / 3600);
        $remainingHours = max(0, $requiredHours - $completedHours);
        $progressPercent = $requiredHours > 0
            ? min(100, (int) round(($completedHours / $requiredHours) * 100))
            : 0;

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
     * Compute net work seconds for one record (excluding lunch break when available).
     */
    private function workedSeconds(object $record): int
    {
        if (! $record->time_in || ! $record->time_out) {
            return 0;
        }

        $date = $record->date instanceof Carbon
            ? $record->date->format('Y-m-d')
            : Carbon::parse((string) $record->date)->format('Y-m-d');

        $timeIn = Carbon::parse($date . ' ' . $record->time_in);
        $timeOut = Carbon::parse($date . ' ' . $record->time_out);

        if ($timeOut->lessThanOrEqualTo($timeIn)) {
            return 0;
        }

        $total = $timeOut->diffInSeconds($timeIn);

        if ($record->lunch_out && $record->lunch_in) {
            $lunchOut = Carbon::parse($date . ' ' . $record->lunch_out);
            $lunchIn = Carbon::parse($date . ' ' . $record->lunch_in);

            if ($lunchIn->greaterThan($lunchOut)) {
                $total -= $lunchIn->diffInSeconds($lunchOut);
            }
        }

        return max(0, $total);
    }
}
