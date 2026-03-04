<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'approved', 'rejected']);

        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(),
            'last_name' => fake()->lastName(),
            'contact_number' => '09'.fake()->numerify('#########'),
            'address' => [
                'province' => fake()->randomElement(['Bulacan', 'Metro Manila', 'Laguna', 'Cavite']),
                'city' => fake()->city(),
                'barangay' => fake()->streetSuffix(),
                'street' => fake()->streetAddress(),
            ],
            'course' => fake()->randomElement(['BSIT', 'BSCS', 'BSIS']),
            'date_of_birth' => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
            'school_attended' => fake()->optional()->company().' University',
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['admin', 'student']),
            'status' => $status,
            'approved_by' => null,
            'approved_at' => $status === 'approved' ? now() : null,
            'admin_notes' => $status !== 'pending' ? fake()->sentence() : null,
            'is_active' => false,
            'last_seen_at' => null,
        ];
    }

}
