<?php

namespace App\Support;

use App\Models\DailyTimeRecord;
use App\Models\User;

/**
 * Converts today's attendance record into a small UI-friendly state object.
 *
 * This keeps button decisions out of Blade so the dashboard can simply ask:
 * "What should the user do next?"
 */
class AttendanceActionState
{
    /**
     * Build the current attendance state for a specific user.
     *
     * We only care about today's record because the dashboard action button
     * should always operate on the current day.
     *
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        // Read only the columns we need for the next-action decision.
        $record = DailyTimeRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first(['id', 'user_id', 'date', 'time_in', 'lunch_out', 'lunch_in', 'time_out']);

        // Delegate the actual decision-making so this method stays small.
        return $this->forRecord($record);
    }

    /**
     * Translate a time record into the next allowed attendance action.
     *
     * The order is intentionally strict:
     * 1. time_in
     * 2. lunch_out
     * 3. lunch_in
     * 4. time_out
     *
     * If everything is filled in, the day is considered complete.
     */
    public function forRecord(?DailyTimeRecord $record): array
    {
        // No record yet means the user has not started the day.
        if (! $record || ! $record->time_in) {
            return $this->makeState(
                action: 'time_in',
                label: 'Time In',
                description: 'Start your work day and create today\'s attendance record.',
                confirmText: 'Record your morning time-in now?',
                confirmationTitle: 'Morning Time-In Confirmation',
                successTitle: 'Time-In Success!',
                successDescription: 'Your morning time-in has been recorded successfully.',
                icon: 'sunrise',
                tone: 'primary'
            );
        }

        // Time in exists, so the next valid action is lunch out.
        if (! $record->lunch_out) {
            return $this->makeState(
                action: 'lunch_out',
                label: 'Lunch Out',
                description: 'Mark the start of your lunch break before stepping away.',
                confirmText: 'Record your lunch-out now?',
                confirmationTitle: 'Lunch-Out Confirmation',
                successTitle: 'Lunch-Out Success!',
                successDescription: 'Your lunch-out time has been recorded successfully.',
                icon: 'lunch-out',
                tone: 'danger'
            );
        }

        // Lunch out exists, so the user must now return from lunch.
        if (! $record->lunch_in) {
            return $this->makeState(
                action: 'lunch_in',
                label: 'Lunch In',
                description: 'Resume your work session after lunch.',
                confirmText: 'Record your lunch-in now?',
                confirmationTitle: 'Afternoon Time-In Confirmation',
                successTitle: 'Time-In Success!',
                successDescription: 'Your afternoon time-in has been recorded successfully.',
                icon: 'lunch-in',
                tone: 'warning'
            );
        }

        // Lunch is complete, so the last step is ending the day.
        if (! $record->time_out) {
            return $this->makeState(
                action: 'time_out',
                label: 'Time Out',
                description: 'Close out your day once your internship work is finished.',
                confirmText: 'Record your time-out for today?',
                confirmationTitle: 'Afternoon Time-Out Confirmation',
                successTitle: 'Time-Out Success!',
                successDescription: 'Your time-out for today has been recorded successfully.',
                icon: 'sunset',
                tone: 'slate'
            );
        }

        // All four timestamps are already present.
        return [
            'action' => null,
            'label' => 'Day Completed',
            'description' => 'All attendance entries for today have already been recorded.',
            'confirmText' => null,
            'confirmationTitle' => null,
            'successTitle' => 'Attendance Complete',
            'successDescription' => 'All attendance entries for today are already recorded.',
            'icon' => 'complete',
            'tone' => 'success',
            'isComplete' => true,
        ];
    }

    /**
     * Small helper to keep the returned array shape consistent.
     *
     * Returning the same keys every time makes the Blade and Livewire code
     * simpler because they don't need to guess which values exist.
     *
     * @return array<string, mixed>
     */
    private function makeState(
        string $action,
        string $label,
        string $description,
        string $confirmText,
        string $confirmationTitle,
        string $successTitle,
        string $successDescription,
        string $icon,
        string $tone
    ): array {
        // This array is intentionally presentation-friendly.
        return [
            'action' => $action,
            'label' => $label,
            'description' => $description,
            'confirmText' => $confirmText,
            'confirmationTitle' => $confirmationTitle,
            'successTitle' => $successTitle,
            'successDescription' => $successDescription,
            'icon' => $icon,
            'tone' => $tone,
            'isComplete' => false,
        ];
    }
}