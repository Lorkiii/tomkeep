<?php

namespace App\Actions\Profile;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Finalizes a student profile and marks it as completed.
 */
class CompleteProfile
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(User $user, array $attributes): User
    {
        return DB::transaction(static function () use ($user, $attributes): User {
            $user->fill([
                'first_name' => $attributes['first_name'] ?? $user->first_name,
                'middle_name' => $attributes['middle_name'] ?? $user->middle_name,
                'last_name' => $attributes['last_name'] ?? $user->last_name,
                'gender' => $attributes['gender'] ?? $user->gender,
                'date_of_birth' => $attributes['date_of_birth'] ?? $user->date_of_birth,
                'address' => $attributes['address'] ?? $user->address,
                'number_of_hours' => (int) ($attributes['number_of_hours'] ?? $user->number_of_hours),
                'contact_number' => $attributes['contact_number'] ?? $user->contact_number,
                'school_attended' => $attributes['school_attended'] ?? $user->school_attended,
                'course' => $attributes['course'] ?? $user->course,
                'profile_completed' => true,
            ]);

            $user->save();

            return $user->refresh();
        });
    }
}
