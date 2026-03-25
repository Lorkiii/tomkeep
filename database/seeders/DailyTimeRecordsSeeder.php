<?php

namespace Database\Seeders;

use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class DailyTimeRecordsSeeder extends Seeder
{
    public function run(): void
    {
        $juan = User::query()->where('email', 'juan@student.local')->first();
        $maria = User::query()->where('email', 'maria@student.local')->first();

        if (! $juan) {
            return;
        }

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $twoDaysAgo = now()->subDays(2)->toDateString();

        // Matches your spreadsheet example:
        // 08:02 -> 17:06 = 9:04 gross, lunch 12:01->13:02 = 1:01, net = 8:03
        $this->upsertDtr($juan->id, $yesterday, [
            'attendance_mode' => 'on_site',
            'time_in' => '08:02:00',
            'lunch_out' => '12:01:00',
            'lunch_in' => '13:02:00',
            'time_out' => '17:06:00',
        ]);

        // Incomplete record (should compute 00:00 / 0 seconds).
        $this->upsertDtr($juan->id, $today, [
            'attendance_mode' => 'on_site',
            'time_in' => '07:58:00',
            'lunch_out' => '12:00:00',
            'lunch_in' => '13:00:00',
            'time_out' => null,
        ]);

        // Another complete record with no lunch entries (should compute full span).
        $this->upsertDtr($juan->id, $twoDaysAgo, [
            'attendance_mode' => 'wfh',
            'time_in' => '09:15:00',
            'lunch_out' => null,
            'lunch_in' => null,
            'time_out' => '15:45:00',
        ]);

        // Add a 2nd approved student’s records for reports/list variety.
        if (! $maria) {
            return;
        }

        // Make Maria approved for attendance testing (also triggers student_code generation).
        if ($maria->status !== 'approved') {
            $maria->forceFill([
                'status' => 'approved',
                'approved_by' => User::query()->where('email', 'admin@timekeep.local')->value('id'),
                'approved_at' => now(),
            ])->save();
        }

        if ($maria->status === 'approved') {
            $this->upsertDtr($maria->id, $yesterday, [
                'attendance_mode' => 'on_site',
                'time_in' => '08:30:00',
                'lunch_out' => '12:05:00',
                'lunch_in' => '13:00:00',
                'time_out' => '17:10:00',
            ]);
        }
    }

    /**
     * @param array{attendance_mode?: string|null, time_in?: string|null, lunch_out?: string|null, lunch_in?: string|null, time_out?: string|null} $times
     */
    private function upsertDtr(int $userId, string $date, array $times): void
    {
        DailyTimeRecord::query()->updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            [
                'attendance_mode' => $times['attendance_mode'] ?? null,
                'time_in' => $times['time_in'] ?? null,
                'lunch_out' => $times['lunch_out'] ?? null,
                'lunch_in' => $times['lunch_in'] ?? null,
                'time_out' => $times['time_out'] ?? null,
            ]
        );
    }
}

