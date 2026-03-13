<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Support\SiteLocationData;
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
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.time_in', '08:00:00');

        $this->assertDatabaseHas('daily_time_records', [
            
            'user_id' => $user->id,
            'time_in' => '08:00:00',
            'attendance_mode' => 'wfh',
            'time_in_latitude' => 14.5995,
            'time_in_longitude' => 120.9842,
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
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:05:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
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
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
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
            'latitude' => 14.5995,
            'longitude' => 120.9842,
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
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_out',
            'occurred_at' => '12:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_in',
            'occurred_at' => '13:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
            'latitude' => 14.59955,
            'longitude' => 120.98425,
        ])->assertOk();

        $record = DailyTimeRecord::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($record);
        $this->assertSame('08:00:00', $record->time_in);
        $this->assertSame('12:00:00', $record->lunch_out);
        $this->assertSame('13:00:00', $record->lunch_in);
        $this->assertSame('17:00:00', $record->time_out);
        $this->assertSame('wfh', $record->attendance_mode);
        $this->assertSame(14.5995, $record->time_in_latitude);
        $this->assertSame(120.9842, $record->time_in_longitude);
        $this->assertSame(14.59955, $record->time_out_latitude);
        $this->assertSame(120.98425, $record->time_out_longitude);

        $this->assertSame(4, AuditLog::query()->where('model_type', 'DailyTimeRecord')->where('model_id', $record->id)->count());
    }

    public function test_location_is_required_before_recording_attendance(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error_code', 'ATTENDANCE_LOCATION_REQUIRED');
    }

    public function test_time_in_inside_an_enabled_site_radius_is_classified_as_on_site(): void
    {
        $this->withoutMiddleware();
        $this->createSiteRecord([
            'allowed_radius_m' => 250,
            'latitude' => 14.5995,
            'longitude' => 120.9842,
            'enforce_geofence' => true,
        ]);

        $user = User::query()->create($this->approvedUserPayload());

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
            'latitude' => 14.5996,
            'longitude' => 120.9843,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.attendance_mode', 'on_site');
    }

    public function test_wfh_time_out_is_rejected_when_far_from_the_original_time_in_location(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_out',
            'occurred_at' => '12:00:00',
            'latitude' => 14.5996,
            'longitude' => 120.9843,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_in',
            'occurred_at' => '13:00:00',
            'latitude' => 14.5997,
            'longitude' => 120.9844,
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
            'latitude' => 14.6200,
            'longitude' => 121.0400,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error_code', 'ATTENDANCE_TIMEOUT_TOO_FAR');
    }

    public function test_wfh_time_out_is_allowed_when_student_stays_within_20_meter_anchor_limit(): void
    {
        $this->withoutMiddleware();
        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_out',
            'occurred_at' => '12:00:00',
            'latitude' => 14.59952,
            'longitude' => 120.98422,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_in',
            'occurred_at' => '13:00:00',
            'latitude' => 14.59953,
            'longitude' => 120.98423,
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
            'latitude' => 14.59955,
            'longitude' => 120.98425,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.time_out', '17:00:00');
    }

    public function test_wfh_time_out_uses_the_global_configured_anchor_limit(): void
    {
        $this->withoutMiddleware();
        Config::set('attendance.wfh_anchor_limit_m', 5000);

        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_out',
            'occurred_at' => '12:00:00',
            'latitude' => 14.5996,
            'longitude' => 120.9843,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_in',
            'occurred_at' => '13:00:00',
            'latitude' => 14.5997,
            'longitude' => 120.9844,
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
            'latitude' => 14.6200,
            'longitude' => 121.0000,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.time_out', '17:00:00');

        $this->assertDatabaseHas('daily_time_records', [
            'user_id' => $user->id,
            'wfh_movement_limit_m' => 5000,
        ]);
    }

    public function test_wfh_time_out_prefers_the_admin_saved_global_limit_over_the_config_fallback(): void
    {
        $this->withoutMiddleware();
        Config::set('attendance.wfh_anchor_limit_m', 20);

        DB::table('system_settings')->insert([
            'key' => 'wfh_anchor_limit_m',
            'value' => '5000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->create($this->approvedUserPayload());

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_in',
            'occurred_at' => '08:00:00',
            'latitude' => 14.5995,
            'longitude' => 120.9842,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_out',
            'occurred_at' => '12:00:00',
            'latitude' => 14.5996,
            'longitude' => 120.9843,
        ])->assertOk();

        $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'lunch_in',
            'occurred_at' => '13:00:00',
            'latitude' => 14.5997,
            'longitude' => 120.9844,
        ])->assertOk();

        $response = $this->postJson('/attendance/mark', [
            'user_id' => $user->id,
            'action' => 'time_out',
            'occurred_at' => '17:00:00',
            'latitude' => 14.6200,
            'longitude' => 121.0000,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.time_out', '17:00:00');

        $this->assertDatabaseHas('daily_time_records', [
            'user_id' => $user->id,
            'wfh_movement_limit_m' => 5000,
        ]);
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

    /**
     * @param array<string, mixed> $overrides
     */
    private function createSiteRecord(array $overrides = []): void
    {
        $attributes = array_merge([
            'company_name' => 'Default Internship Site',
            'address' => [
                'street_address' => '1 Example Street',
                'barangay' => 'Barangay Uno',
                'municipality' => 'Makati City',
                'province' => 'Metro Manila',
            ],
            'allowed_radius_m' => 150,
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'enforce_geofence' => true,
            'is_active' => true,
        ], $overrides);

        $siteLocationData = app(SiteLocationData::class);

        DB::table('sites')->insert([
            ...$siteLocationData->payload($attributes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
