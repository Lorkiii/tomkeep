<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_code_is_created_only_when_user_is_approved(): void
    {
        $user = User::query()->create($this->pendingUserPayload('student1@mail.com'));

        $this->assertNull($user->student_code);

        $user->update([
            'status' => 'approved',
            'approved_at' => '2026-03-05 08:00:00',
        ]);

        $user->refresh();

        $this->assertNotNull($user->student_code);
        $this->assertMatchesRegularExpression('/^ST2603\d{5,}$/', $user->student_code);
    }

    public function test_student_code_is_unique_for_multiple_approved_users_in_same_month(): void
    {
        $first = User::query()->create($this->pendingUserPayload('student2@mail.com'));
        $second = User::query()->create($this->pendingUserPayload('student3@mail.com'));

        $first->update([
            'status' => 'approved',
            'approved_at' => '2026-03-06 09:00:00',
        ]);

        $second->update([
            'status' => 'approved',
            'approved_at' => '2026-03-06 10:00:00',
        ]);

        $first->refresh();
        $second->refresh();

        $this->assertNotNull($first->student_code);
        $this->assertNotNull($second->student_code);
        $this->assertNotSame($first->student_code, $second->student_code);
    }

    /**
     * @return array<string, mixed>
     */
    private function pendingUserPayload(string $email): array
    {
        return [
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => 'Student',
            'contact_number' => '09123456789',
            'address' => [
                'province' => 'Metro Manila',
                'municipality' => 'Manila',
                'street_house_number' => '123',
            ],
            'course' => 'BSIT',
            'date_of_birth' => '2004-01-15',
            'school_attended' => 'Sample School',
            'number_of_hours' => 0,
            'email' => $email,
            'password' => 'secret123',
            'role' => 'student',
            'status' => 'pending',
            'is_active' => false,
        ];
    }
}
