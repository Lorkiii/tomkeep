<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AttendancePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendancePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_attendance_policy_page(): void
    {
        $admin = User::query()->create($this->adminPayload());

        $this->actingAs($admin)
            ->get(route('admin.settings.attendance.edit'))
            ->assertOk()
            ->assertSeeText('Attendance Policy')
            ->assertSee('name="wfh_anchor_limit_m"', false);
    }

    public function test_admin_can_update_the_global_wfh_anchor_limit(): void
    {
        $admin = User::query()->create($this->adminPayload());

        $response = $this->actingAs($admin)->patch(route('admin.settings.attendance.update'), [
            'wfh_anchor_limit_m' => 85,
        ]);

        $response->assertRedirect(route('admin.settings.attendance.edit'));

        $this->assertSame(85, app(AttendancePolicy::class)->wfhAnchorLimitMeters());

        $this->assertDatabaseHas('system_settings', [
            'key' => 'wfh_anchor_limit_m',
            'value' => '85',
            'updated_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'attendance_policy_updated',
            'model_type' => 'SystemSetting',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function adminPayload(array $overrides = []): array
    {
        return array_merge([
            'username' => 'admin' . uniqid(),
            'first_name' => 'System',
            'middle_name' => null,
            'last_name' => 'Admin',
            'gender' => null,
            'contact_number' => '09123456789',
            'address' => [
                'province' => 'Metro Manila',
                'city' => 'Manila',
                'barangay' => 'Barangay 1',
                'street' => 'Admin Street',
            ],
            'course' => null,
            'date_of_birth' => '1995-01-15',
            'school_attended' => null,
            'number_of_hours' => 0,
            'profile_completed' => true,
            'email' => 'admin' . uniqid() . '@mail.com',
            'password' => 'secret123',
            'role' => 'admin',
            'status' => 'approved',
            'approved_at' => now(),
            'admin_notes' => null,
            'is_active' => false,
        ], $overrides);
    }
}