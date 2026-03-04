<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminId = DB::table('users')->insertGetId([
            'first_name' => 'System',
            'middle_name' => null,
            'last_name' => 'Admin',
            'contact_number' => '09170000001',
            'address' => json_encode([
                'province' => 'Metro Manila',
                'city' => 'Manila',
                'barangay' => 'Barangay 659-A',
                'street' => 'City Hall, Antonio Villegas Road',
            ]),
            'course' => 'BSIT',
            'date_of_birth' => '1998-05-15',
            'school_attended' => 'Tech University',
            'email' => 'admin@timekeep.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'approved',
            'approved_by' => null,
            'approved_at' => now(),
            'admin_notes' => 'Initial seeded admin account.',
            'remember_token' => Str::random(10),
            'is_active' => false,
            'last_seen_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student1Id = DB::table('users')->insertGetId([
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'contact_number' => '09170000002',
            'address' => json_encode([
                'province' => 'Metro Manila',
                'city' => 'Quezon City',
                'barangay' => 'Batasan Hills',
                'street' => 'No. 45 Maharlika Street',
            ]),
            'course' => 'BSIT',
            'date_of_birth' => '2003-02-20',
            'school_attended' => 'State University',
            'email' => 'juan@student.local',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'admin_notes' => 'Approved for OJT timekeeping.',
            'remember_token' => Str::random(10),
            'is_active' => false,
            'last_seen_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student2Id = DB::table('users')->insertGetId([
            'first_name' => 'Maria',
            'middle_name' => 'Reyes',
            'last_name' => 'Lopez',
            'contact_number' => '09170000003',
            'address' => json_encode([
                'province' => 'Metro Manila',
                'city' => 'Makati City',
                'barangay' => 'Poblacion',
                'street' => 'Unit 7, Riverside Drive',
            ]),
            'course' => 'BSCS',
            'date_of_birth' => '2002-11-10',
            'school_attended' => 'National College',
            'email' => 'maria@student.local',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'admin_notes' => null,
            'remember_token' => Str::random(10),
            'is_active' => false,
            'last_seen_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::statement("INSERT INTO sites (company_name, address, allowed_radius_m, location, is_active, created_at, updated_at)
            VALUES
            ('Main OJT Site', JSON_OBJECT('full', 'Ayala Avenue, Makati'), 100, ST_GeomFromText('POINT(121.024445 14.554729)'), 1, NOW(), NOW()),
            ('Branch Office', JSON_OBJECT('full', 'Ortigas Center, Pasig'), 150, ST_GeomFromText('POINT(121.060530 14.586690)'), 1, NOW(), NOW())");

        DB::table('daily_time_records')->insert([
            [
                'user_id' => $student1Id,
                'date' => now()->subDays(1)->toDateString(),
                'time_in' => '08:02:00',
                'lunch_out' => '12:01:00',
                'lunch_in' => '13:02:00',
                'time_out' => '17:06:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $student1Id,
                'date' => now()->toDateString(),
                'time_in' => '07:58:00',
                'lunch_out' => '12:00:00',
                'lunch_in' => '13:00:00',
                'time_out' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('audit_logs')->insert([
            [
                'user_id' => $adminId,
                'action' => 'approve',
                'model_type' => 'User',
                'model_id' => $student1Id,
                'old_values' => json_encode(['status' => 'pending']),
                'new_values' => json_encode(['status' => 'approved']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'DatabaseSeeder',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $student1Id,
                'action' => 'time_in',
                'model_type' => 'DailyTimeRecord',
                'model_id' => null,
                'old_values' => null,
                'new_values' => json_encode(['time_in' => now()->toDateString().' 07:58:00']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'DatabaseSeeder',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'action' => 'registration',
                'model_type' => 'User',
                'model_id' => $student2Id,
                'old_values' => null,
                'new_values' => json_encode(['status' => 'pending']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'DatabaseSeeder',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
