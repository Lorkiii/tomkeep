<?php

namespace Tests\Feature;

use App\Models\DailyTimeRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 5: Feature Tests for Admin Attendance Monitoring
 * Validates today's attendance view, reports page, and CSV export functionality.
 * Tests ensure real-time data display, filtering, and export generation work correctly.
 */
class AdminAttendanceMonitoringTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Admin can navigate to today's attendance page and see student records.
     * Validates: Page loads, attendance data displays, in-progress status is detected.
     */
    public function test_admin_can_view_todays_attendance_page(): void
    {
        // Create admin user
        $admin = User::query()->create($this->adminPayload());
        // Create student with attendance data
        $student = User::query()->create($this->studentPayload([
            'first_name' => 'Mara',
            'last_name' => 'Santos',
            'email' => 'mara.santos@mail.com',
            'student_code' => 'ST2603001',
        ]));

        // Create in-progress attendance record (no time_out)
        DailyTimeRecord::query()->create([
            'user_id' => $student->id,
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'lunch_out' => '12:00:00',
            'lunch_in' => '13:00:00',
            'time_out' => null,
        ]);

        // Test: GET the today attendance page as admin
        $this->actingAs($admin)
            ->get(route('admin.attendance.today'))
            // Assert page loaded successfully
            ->assertOk()
            // Assert page header is present
            ->assertSeeText('Attendance Monitoring')
            // Assert student data appears on page
            ->assertSeeText('Mara Santos')
            // Assert status badge shows in-progress
            ->assertSeeText('In Progress');
    }

    /**
     * Test: Admin can view reports page with statistics cards and trend data.
     * Validates: Page loads, daily trend chart renders, records display correctly.
     */
    public function test_admin_can_view_reports_page_with_statistics_and_records(): void
    {
        // Create admin user
        $admin = User::query()->create($this->adminPayload());
        // Create student with completed attendance record
        $student = User::query()->create($this->studentPayload([
            'first_name' => 'Jared',
            'last_name' => 'Flores',
            'email' => 'jared.flores@mail.com',
            'student_code' => 'ST2603002',
        ]));

        // Create completed attendance record (includes time_out)
        DailyTimeRecord::query()->create([
            'user_id' => $student->id,
            'date' => now()->subDay()->toDateString(),
            'time_in' => '08:10:00',
            'lunch_out' => '12:05:00',
            'lunch_in' => '12:55:00',
            'time_out' => '17:15:00',
        ]);

        // Test: GET the reports page as admin
        $this->actingAs($admin)
            ->get(route('admin.attendance.reports'))
            // Assert page loaded successfully
            ->assertOk()
            // Assert page title is present
            ->assertSeeText('Attendance Reports')
            // Assert daily trend chart section renders
            ->assertSeeText('Daily Worked Hours Trend')
            // Assert student data appears on page
            ->assertSeeText('Jared Flores');
    }

    /**
     * Test: Admin can export filtered attendance records as CSV.
     * Validates: Export route accepts parameters, returns CSV headers, contains expected data.
     */
    public function test_admin_can_export_attendance_csv_report(): void
    {
        // Create admin user
        $admin = User::query()->create($this->adminPayload());
        // Create student with attendance record
        $student = User::query()->create($this->studentPayload([
            'first_name' => 'Ana',
            'last_name' => 'Mendoza',
            'email' => 'ana.mendoza@mail.com',
            'student_code' => 'ST2603003',
        ]));

        // Create attendance record for export
        DailyTimeRecord::query()->create([
            'user_id' => $student->id,
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'lunch_out' => '12:00:00',
            'lunch_in' => '13:00:00',
            'time_out' => '17:00:00',
        ]);

        // Test: GET the export endpoint with date range parameters
        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.reports.export', [
                'from' => now()->subDays(2)->toDateString(),
                'to' => now()->toDateString(),
            ]));

        // Assert export succeeded
        $response->assertOk();
        // Assert response headers are correctly set for CSV download
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Get streamed CSV content
        $content = $response->streamedContent();

        // Assert CSV contains expected headers and data
        $this->assertStringContainsString('Student Name', $content);
        $this->assertStringContainsString('Ana Mendoza', $content);
        $this->assertStringContainsString('08:00:00', $content);
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
            'status' => 'approved',
            'approved_at' => now()->subDay(),
            'approved_by' => null,
            'admin_notes' => null,
            'is_active' => true,
        ], $overrides);
    }
}