<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@timekeep.local'],
            [
                'username' => 'admin',
                'first_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Admin',
                'gender' => 'Other',
                'contact_number' => '09170000001',
                'address' => [
                    'province' => 'Metro Manila',
                    'city' => 'Manila',
                    'barangay' => 'Barangay 659-A',
                    'street' => 'City Hall, Antonio Villegas Road',
                ],
                'course' => 'BSIT',
                'date_of_birth' => '1998-05-15',
                'school_attended' => 'Tech University',
                'number_of_hours' => 0,
                'profile_completed' => true,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'approved',
                'approved_by' => null,
                'approved_at' => now(),
                'admin_notes' => 'Initial seeded admin account.',
                'is_active' => false,
                'last_seen_at' => null,
            ]
        );

        // Ensure deterministic baseline fields if the seeder is re-run.
        $admin->forceFill([
            'username' => $admin->username ?? 'admin',
            'role' => 'admin',
            'status' => 'approved',
            'approved_by' => null,
            'approved_at' => $admin->approved_at ?? now(),
        ])->save();

        User::query()->firstOrCreate(
            ['email' => 'juan@student.local'],
            [
                'username' => 'juan',
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'gender' => 'Male',
                'contact_number' => '09170000002',
                'address' => [
                    'province' => 'Metro Manila',
                    'city' => 'Quezon City',
                    'barangay' => 'Batasan Hills',
                    'street' => 'No. 45 Maharlika Street',
                ],
                'course' => 'BSIT',
                'date_of_birth' => '2003-02-20',
                'school_attended' => 'State University',
                'number_of_hours' => 400,
                'profile_completed' => true,
                'password' => Hash::make('password123'),
                'role' => 'student',
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'admin_notes' => 'Approved for OJT timekeeping.',
                'is_active' => false,
                'last_seen_at' => null,
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'maria@student.local'],
            [
                'username' => 'maria',
                'first_name' => 'Maria',
                'middle_name' => 'Reyes',
                'last_name' => 'Lopez',
                'gender' => 'Female',
                'contact_number' => '09170000003',
                'address' => [
                    'province' => 'Metro Manila',
                    'city' => 'Makati City',
                    'barangay' => 'Poblacion',
                    'street' => 'Unit 7, Riverside Drive',
                ],
                'course' => 'BSCS',
                'date_of_birth' => '2002-11-10',
                'school_attended' => 'National College',
                'number_of_hours' => 350,
                'profile_completed' => true,
                'password' => Hash::make('password123'),
                'role' => 'student',
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'admin_notes' => null,
                'is_active' => false,
                'last_seen_at' => null,
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'carlo@student.local'],
            [
                'username' => 'carlo',
                'first_name' => 'Carlo',
                'middle_name' => 'Garcia',
                'last_name' => 'Ramos',
                'gender' => 'Male',
                'contact_number' => '09170000004',
                'address' => [
                    'province' => 'Laguna',
                    'city' => 'Calamba City',
                    'barangay' => 'Real',
                    'street' => 'Blk 3 Lot 12 Rizal Street',
                ],
                'course' => 'BSIS',
                'date_of_birth' => '2002-07-08',
                'school_attended' => 'Provincial Institute',
                'number_of_hours' => 300,
                'profile_completed' => true,
                'password' => Hash::make('password123'),
                'role' => 'student',
                'status' => 'rejected',
                'approved_by' => $admin->id,
                'approved_at' => null,
                'admin_notes' => 'Requirements incomplete.',
                'is_active' => false,
                'last_seen_at' => null,
            ]
        );
    }
}

