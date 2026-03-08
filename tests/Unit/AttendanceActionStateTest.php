<?php

namespace Tests\Unit;

use App\Models\DailyTimeRecord;
use App\Support\AttendanceActionState;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for the small state machine behind the floating attendance button.
 */
class AttendanceActionStateTest extends TestCase
{
    #[Test]
    public function it_returns_time_in_when_no_record_exists(): void
    {
        // No record means the student has not started today yet.
        $state = app(AttendanceActionState::class)->forRecord(null);

        $this->assertSame('time_in', $state['action']);
        $this->assertSame('Time In', $state['label']);
        $this->assertFalse($state['isComplete']);
    }

    #[Test]
    public function it_advances_through_the_required_attendance_sequence(): void
    {
        // Resolve the support class from Laravel's container.
        $support = app(AttendanceActionState::class);

        // Only time in exists, so lunch out should be next.
        $timeInOnly = new DailyTimeRecord([
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
        ]);

        // Lunch out exists, so lunch in should be next.
        $waitingForLunchIn = new DailyTimeRecord([
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'lunch_out' => '12:00:00',
        ]);

        // Lunch is finished, so time out should be next.
        $waitingForTimeOut = new DailyTimeRecord([
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'lunch_out' => '12:00:00',
            'lunch_in' => '13:00:00',
        ]);

        // Fully completed day should produce no next action.
        $completed = new DailyTimeRecord([
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'lunch_out' => '12:00:00',
            'lunch_in' => '13:00:00',
            'time_out' => '17:00:00',
        ]);

        // Assert the step-by-step sequence is preserved.
        $this->assertSame('lunch_out', $support->forRecord($timeInOnly)['action']);
        $this->assertSame('lunch_in', $support->forRecord($waitingForLunchIn)['action']);
        $this->assertSame('time_out', $support->forRecord($waitingForTimeOut)['action']);
        $this->assertTrue($support->forRecord($completed)['isComplete']);
        $this->assertNull($support->forRecord($completed)['action']);
    }
}