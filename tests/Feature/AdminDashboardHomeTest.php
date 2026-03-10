<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_home_receives_overview_metrics_and_recent_queue_data(): void
    {
        $admin = User::query()->create($this->adminPayload());

        $pendingComplete = User::query()->create($this->studentPayload([
            'first_name' => 'Pending',
            'last_name' => 'Complete',
            'email' => 'pending-complete@mail.com',
            'created_at' => now()->subHours(2),
            'profile_completed' => true,
            'status' => 'pending',
        ]));

        $pendingIncomplete = User::query()->create($this->studentPayload([
            'first_name' => 'Pending',
            'last_name' => 'Incomplete',
            'email' => 'pending-incomplete@mail.com',
            'created_at' => now()->subDays(3),
            'profile_completed' => false,
            'status' => 'pending',
        ]));

        $approvedToday = User::query()->create($this->studentPayload([
            'first_name' => 'Approved',
            'last_name' => 'Today',
            'email' => 'approved-today@mail.com',
            'status' => 'approved',
            'approved_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
            'is_active' => true,
            'admin_notes' => 'Approved for dashboard access.',
        ]));

        $rejected = User::query()->create($this->studentPayload([
            'first_name' => 'Rejected',
            'last_name' => 'Student',
            'email' => 'rejected@mail.com',
            'status' => 'rejected',
            'updated_at' => now()->subMinutes(30),
            'admin_notes' => 'Missing internship details.',
        ]));

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('totalStudents', 4)
            ->assertViewHas('pendingApprovals', 2)
            ->assertViewHas('approvedToday', 1)
            ->assertViewHas('activeNow', 1)
            ->assertViewHas('recentRequests', function (array $requests) use ($pendingComplete, $pendingIncomplete): bool {
                $ids = array_column($requests, 'id');

                return in_array($pendingComplete->id, $ids, true)
                    && in_array($pendingIncomplete->id, $ids, true);
            })
            ->assertViewHas('recentDecisions', function (array $decisions) use ($approvedToday, $rejected): bool {
                $ids = array_column($decisions, 'id');

                return in_array($approvedToday->id, $ids, true)
                    && in_array($rejected->id, $ids, true);
            })
            ->assertSeeText('Operations Overview')
            ->assertSeeText('Pending approvals need review')
            ->assertSeeText('Pending Complete')
            ->assertSeeText('Rejected Student');
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

    /**
     * @return array<string, mixed>
     */
    private function studentPayload(array $overrides = []): array
    {
        return array_merge([
            'username' => 'student' . uniqid(),
            'first_name' => 'Juan',
            'middle_name' => null,
            'last_name' => 'Dela Cruz',
            'gender' => 'Male',
            'contact_number' => '09123456789',
            'address' => [
                'province' => 'Metro Manila',
                'city' => 'Manila',
                'barangay' => 'Barangay 2',
                'street' => 'Student Street',
            ],
            'course' => 'BSIT',
            'date_of_birth' => '2004-01-15',
            'school_attended' => 'Sample School',
            'number_of_hours' => 486,
            'profile_completed' => true,
            'email' => 'student' . uniqid() . '@mail.com',
            'password' => 'secret123',
            'role' => 'student',
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'admin_notes' => null,
            'is_active' => false,
        ], $overrides);
    }
}