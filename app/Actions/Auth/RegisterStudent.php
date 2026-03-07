<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Handles student registration persistence in one transaction.
 */
class RegisterStudent
{
    /**
     * Create a pending student account with default profile placeholders.
     */
    public function execute(string $email, string $username, string $password): User
    {
        return DB::transaction(static function () use ($email, $username, $password): User {
            return User::query()->create([
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'role' => 'student',
                'status' => 'pending',
                'first_name' => null,
                'middle_name' => null,
                'last_name' => null,
                'gender' => null,
                'date_of_birth' => null,
                'contact_number' => null,
                'address' => null,
                'course' => null,
                'school_attended' => null,
                'number_of_hours' => 0,
                'profile_completed' => false,
            ]);
        });
    }
}
