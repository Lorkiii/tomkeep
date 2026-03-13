<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_index_and_detail_pages(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload([
            'first_name' => 'Maria',
            'last_name' => 'Lopez',
            'email' => 'maria-lopez@mail.com',
        ]));

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeText('User Management')
            ->assertSeeText('Maria Lopez')
            ->assertSeeText('maria-lopez@mail.com');

        $this->actingAs($admin)
            ->get(route('admin.users.show', $student))
            ->assertOk()
            ->assertSeeText('Maria Lopez')
            ->assertSeeText('Status Actions');
    }

    public function test_admin_can_update_user_profile_details(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload());

        $response = $this->actingAs($admin)->patch(route('admin.users.update', $student), [
            'username' => 'updated.student',
            'email' => 'updated-student@mail.com',
            'role' => 'student',
            'position' => 'Student Intern',
            'first_name' => 'Updated',
            'middle_name' => 'Profile',
            'last_name' => 'Student',
            'gender' => 'Female',
            'date_of_birth' => '2004-02-20',
            'contact_number' => '09991234567',
            'school_attended' => 'Updated University',
            'course' => 'BSCS',
            'number_of_hours' => 600,
            'province' => 'Bulacan',
            'municipality' => 'Malolos',
            'barangay' => 'Santo Rosario',
            'street_house_number' => '123 Rizal Street',
            'admin_notes' => 'Profile updated by admin.',
            'profile_completed' => '1',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users.show', $student));

        $student->refresh();

        $this->assertSame('updated.student', $student->username);
        $this->assertSame('updated-student@mail.com', $student->email);
        $this->assertSame('Student Intern', $student->position);
        $this->assertSame('Updated', $student->first_name);
        $this->assertSame('Profile', $student->middle_name);
        $this->assertSame('Student', $student->last_name);
        $this->assertSame('Female', $student->gender);
        $this->assertSame('09991234567', $student->contact_number);
        $this->assertSame('Updated University', $student->school_attended);
        $this->assertSame('BSCS', $student->course);
        $this->assertSame(600, $student->number_of_hours);
        $this->assertTrue($student->profile_completed);
        $this->assertTrue($student->is_active);
        $this->assertSame('Bulacan', $student->address['province']);
        $this->assertSame('Malolos', $student->address['municipality']);
        $this->assertSame('Profile updated by admin.', $student->admin_notes);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_profile_updated',
            'user_id' => $admin->id,
            'model_id' => $student->id,
        ]);
    }

    public function test_admin_can_change_user_status_from_detail_page(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload([
            'status' => 'pending',
            'profile_completed' => true,
        ]));

        $response = $this->actingAs($admin)->patch(route('admin.users.status', $student), [
            'status' => 'approved',
            'admin_notes' => 'Approved from user management.',
        ]);

        $response->assertRedirect(route('admin.users.show', $student));

        $student->refresh();

        $this->assertSame('approved', $student->status);
        $this->assertSame($admin->id, $student->approved_by);
        $this->assertNotNull($student->approved_at);
        $this->assertNotNull($student->student_code);
        $this->assertSame('Approved from user management.', $student->admin_notes);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'student_approved',
            'user_id' => $admin->id,
            'model_id' => $student->id,
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
                'municipality' => 'Manila',
                'barangay' => 'Barangay 2',
                'street_house_number' => 'Student Street',
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