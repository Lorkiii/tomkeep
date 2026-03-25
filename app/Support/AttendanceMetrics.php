<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Phase 5: Shared attendance math helpers used by admin monitoring and reporting screens.
 * Centralizes time calculation logic to ensure consistency across today view, reports page, and CSV exports.
 */
class AttendanceMetrics
{
    /**
     * Compute net worked seconds from one daily record.
     * Subtracts lunch break interval if both lunch_out and lunch_in are recorded.
     * Returns 0 if time_in or time_out is missing (incomplete record).
     *
     * @param array<string, mixed> $record Array containing 'time_in', 'time_out', 'lunch_out', 'lunch_in' keys
     * @return int Net worked seconds after deducting lunch interval
     */
    public function workedSeconds(array $record): int
    {
        // Guard: incomplete time records cannot produce valid worked hours
        if (empty($record['time_in']) || empty($record['time_out'])) {
            return 0;
        }

        // Normalize record date to YYYY-MM-DD for time parsing
        $date = $this->recordDate($record)->format('Y-m-d');
        // Parse time values by combining date + time strings (e.g., "2026-03-10 08:00:00")
        $timeIn = Carbon::parse($date . ' ' . $record['time_in']);
        $timeOut = Carbon::parse($date . ' ' . $record['time_out']);

        // Guard: invalid time sequence (time out before or equal to time in)
        if ($timeOut->lessThanOrEqualTo($timeIn)) {
            return 0;
        }

        // Calculate gross worked time (total duration)
        $total = $timeIn->diffInSeconds($timeOut);

        // Deduct lunch break if both lunch timestamps are present
        if (! empty($record['lunch_out']) && ! empty($record['lunch_in'])) {
            $lunchOut = Carbon::parse($date . ' ' . $record['lunch_out']);
            $lunchIn = Carbon::parse($date . ' ' . $record['lunch_in']);

            // Only subtract lunch if lunch_in is after lunch_out (valid lunch period)
            if ($lunchIn->greaterThan($lunchOut)) {
                $total -= $lunchOut->diffInSeconds($lunchIn);
            }
        }

        // Return net worked time, ensuring non-negative value
        return max(0, $total);
    }

    /**
     * Render seconds into a fixed HH:MM display for reports and cards.
     * Always zero-pads both hours and minutes for consistent formatting (e.g., "09:30").
     *
     * @param int $seconds Total seconds worked (e.g., 30600 → "08:30")
     * @return string Formatted as 'HH:MM' (always 5 characters)
     */
    public function formatHoursMinutes(int $seconds): string
    {
        // Protect against negative: enforce non-negative input
        $safe = max(0, $seconds);
        // Integer division: 3600 seconds per hour
        $hours = intdiv($safe, 3600);
        // Modulo remainder: convert remaining seconds to minutes
        $minutes = intdiv($safe % 3600, 60);

        // Pad both values to 2 digits with leading zeros (e.g., 5 → "05")
        return str_pad((string) $hours, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Normalize mixed date payloads into a Carbon instance.
     * Handles Carbon objects, date strings, and missing dates gracefully.
     *
     * @param array<string, mixed> $record Array with optional 'date' key
     * @return CarbonInterface Parsed date or today if date key is missing
     */
    public function recordDate(array $record): CarbonInterface
    {
        // Use provided date or default to today
        $date = $record['date'] ?? now()->toDateString();

        // If already a Carbon instance, return as-is
        if ($date instanceof CarbonInterface) {
            return $date;
        }

        // Parse string dates (e.g., "2026-03-10" or "2026-03-10 08:00:00")
        return Carbon::parse((string) $date);
    }
}