<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_user_can_time_in_and_writes_audit_log(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.time_in', '08:00:00');

        $this->assertDatabaseHas('daily_time_records', [
            
            'user_id' => $user->id,
            'time_in' => '08:00:00',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'attendance_action',
            'model_type' => 'DailyTimeRecord',
            'user_id' => $user->id,
        ]);
    }

    public function test_duplicate_time_in_is_rejected(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:05:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error_code', 'ATTENDANCE_ALREADY_SET');
    }

    public function test_out_of_order_time_out_is_rejected(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error_code', 'ATTENDANCE_OUT_OF_ORDER');
    }

    public function test_non_approved_user_is_rejected(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload(['status' => 'pending']));

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('error_code', 'USER_NOT_APPROVED');
    }

    public function test_full_sequence_completes_and_logs_all_actions(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_out',
            'occurred_at' => '12:00:00',
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_in',
            'occurred_at' => '13:00:00',
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
        ])->assertOk();

        $record = DailyTimeRecord::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($record);
        $this->assertSame('08:00:00', $record->time_in);
        $this->assertSame('12:00:00', $record->lunch_out);
        $this->assertSame('13:00:00', $record->lunch_in);
        $this->assertSame('17:00:00', $record->time_out);

        $this->assertSame(4, AuditLog::query()->where('model_type', 'DailyTimeRecord')->where('model_id', $record->id)->count());
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function approvedUserPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Juan',
            'middle_name' => null,
            'last_name' => 'Dela Cruz',
            'contact_number' => '09123456789',
            'address' => 'Manila',
            'course' => 'BSIT',
            'date_of_birth' => '2004-01-15',
            'school_attended' => 'Sample School',
            'email' => 'student'.uniqid().'@mail.com',
            'password' => 'secret123',
            'role' => 'student',
            'status' => 'approved',
            'is_active' => false,
        ], $overrides);
    }
}
