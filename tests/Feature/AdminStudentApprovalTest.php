<?php

namespace Tests\Feature;

use App\Livewire\Admin\StudentApprovalTable;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminStudentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_a_completed_pending_student(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload());

        Livewire::actingAs($admin)
            ->test(StudentApprovalTable::class)
            ->set("notes.{$student->id}", 'Profile verified and approved.')
            ->call('promptApprove', $student->id)
            ->assertSet('showConfirmationModal', true)
            ->assertSet('pendingStudentId', $student->id)
            ->assertSet('pendingAction', 'approve')
            ->call('confirmPendingAction')
            ->assertSet('showConfirmationModal', false)
            ->assertSet('feedback.type', 'success');

        $student->refresh();

        $this->assertSame('approved', $student->status);
        $this->assertSame($admin->id, $student->approved_by);
        $this->assertNotNull($student->approved_at);
        $this->assertNotNull($student->student_code);
        $this->assertSame('Profile verified and approved.', $student->admin_notes);

        $log = AuditLog::query()->where('action', 'student_approved')->first();

        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame($student->id, $log->model_id);
    }

    public function test_admin_can_reject_a_pending_student_with_notes(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload());

        Livewire::actingAs($admin)
            ->test(StudentApprovalTable::class)
            ->set("notes.{$student->id}", 'Missing internship details.')
            ->call('promptReject', $student->id)
            ->assertSet('showConfirmationModal', true)
            ->assertSet('pendingStudentId', $student->id)
            ->assertSet('pendingAction', 'reject')
            ->call('confirmPendingAction')
            ->assertSet('showConfirmationModal', false)
            ->assertSet('feedback.type', 'success');

        $student->refresh();

        $this->assertSame('rejected', $student->status);
        $this->assertNull($student->approved_by);
        $this->assertNull($student->approved_at);
        $this->assertSame('Missing internship details.', $student->admin_notes);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'student_rejected',
            'user_id' => $admin->id,
            'model_id' => $student->id,
        ]);
    }

    public function test_admin_cannot_approve_a_student_with_incomplete_profile(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload([
            'profile_completed' => false,
        ]));

        Livewire::actingAs($admin)
            ->test(StudentApprovalTable::class)
            ->call('promptApprove', $student->id)
            ->assertSet('showConfirmationModal', false)
            ->assertSet('feedback.type', 'error');

        $student->refresh();

        $this->assertSame('pending', $student->status);
        $this->assertNull($student->approved_by);
        $this->assertNull($student->approved_at);
        $this->assertNull($student->student_code);
        $this->assertSame(0, AuditLog::query()->count());
    }

    public function test_admin_can_cancel_confirmation_without_updating_student_status(): void
    {
        $admin = User::query()->create($this->adminPayload());
        $student = User::query()->create($this->studentPayload());

        Livewire::actingAs($admin)
            ->test(StudentApprovalTable::class)
            ->call('promptApprove', $student->id)
            ->assertSet('showConfirmationModal', true)
            ->call('closeConfirmationModal')
            ->assertSet('showConfirmationModal', false)
            ->assertSet('pendingStudentId', null)
            ->assertSet('pendingAction', null);

        $student->refresh();

        $this->assertSame('pending', $student->status);
        $this->assertNull($student->approved_by);
        $this->assertNull($student->approved_at);
        $this->assertSame(0, AuditLog::query()->count());
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