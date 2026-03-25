<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();

            // Company / OJT site info
            $table->string('company_name');
            $table->json('address')->nullable();

            // Allowed radius in meters
            $table->unsignedInteger('allowed_radius_m')->default(100);

            // Toggle whether this site's radius should classify logs as on-site.
            $table->boolean('enforce_geofence')->default(true);

            // GPS location (POINT SRID 4326 added via raw SQL)

            // Optional status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
        
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE sites ADD COLUMN location POINT NOT NULL AFTER allowed_radius_m');
            DB::statement('ALTER TABLE sites ADD SPATIAL INDEX sites_location_spatial_index (location)');

            return;
        }

        Schema::table('sites', function (Blueprint $table) {
            $table->string('location')->nullable()->after('allowed_radius_m');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};