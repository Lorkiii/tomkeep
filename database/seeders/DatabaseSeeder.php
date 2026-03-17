<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AuditLogsSeeder;
use Database\Seeders\DailyTimeRecordsSeeder;
use Database\Seeders\SitesSeeder;
use Database\Seeders\UsersSeeder;

/**
 * Seeds baseline local testing data (admin, students, sites, DTR, and audit logs).
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Keep DatabaseSeeder stable and delegate to smaller seeders so
        // individual parts can be rerun/tested without DB-driver-specific SQL.
        $this->call([
            UsersSeeder::class,
            SitesSeeder::class,
            DailyTimeRecordsSeeder::class,
            AuditLogsSeeder::class,
        ]);
    }
}
