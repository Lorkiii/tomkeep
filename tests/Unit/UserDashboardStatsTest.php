<?php

namespace Tests\Unit;

use App\Models\DailyTimeRecord;
use App\Models\User;
use App\Support\UserDashboardStats;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for dashboard summary generation.
 */
class UserDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_only_returns_today_logs_for_the_dashboard_recent_logs_section(): void
    {
        // Freeze time so all "today" comparisons are deterministic.
        Carbon::setTestNow('2026-03-08 09:30:00');

        // Create an approved student because the dashboard is student-oriented.
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'approved',
            'number_of_hours' => 400,
        ]);

        // Record for today: should appear in recent logs.
        DailyTimeRecord::query()->create([
            'user_id' => $user->id,
            'date' => '2026-03-08',
            'time_in' => '08:00:00',
            'lunch_out' => '12:00:00',
            'lunch_in' => '13:00:00',
            'time_out' => '17:00:00',
        ]);

        // Record for yesterday: should affect totals, but not the recent log list.
        DailyTimeRecord::query()->create([
            'user_id' => $user->id,
            'date' => '2026-03-07',
            'time_in' => '08:15:00',
            'time_out' => '17:15:00',
        ]);

        // Build the dashboard payload.
        $stats = app(UserDashboardStats::class)->forUser($user);

        // Four actions from today should appear: in, lunch out, lunch in, out.
        $this->assertCount(4, $stats['activityLogs']);
        $this->assertSame('Time-Out Work on Office', $stats['activityLogs'][0]['label']);
        $this->assertSame('Lunch-In', $stats['activityLogs'][1]['label']);
        $this->assertSame('Lunch-Out', $stats['activityLogs'][2]['label']);
        $this->assertSame('Time-In Work on Office', $stats['activityLogs'][3]['label']);

        // Every dashboard recent log should belong to the frozen "today" date.
        foreach ($stats['activityLogs'] as $log) {
            $this->assertStringStartsWith('2026-03-08', $log['at']);
        }

        // Clear the fake clock so other tests use the real current time.
        Carbon::setTestNow();
    }
}