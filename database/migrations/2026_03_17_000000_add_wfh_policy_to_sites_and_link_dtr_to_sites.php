<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->boolean('wfh_anchor_enforced')
                ->default(true)
                ->after('enforce_geofence');
            $table->unsignedInteger('wfh_anchor_limit_m')
                ->nullable()
                ->after('wfh_anchor_enforced');
        });

        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->foreignId('site_id')
                ->nullable()
                ->after('user_id')
                ->constrained('sites')
                ->nullOnDelete();

            $table->index(['site_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->dropIndex(['site_id', 'date']);
            $table->dropConstrainedForeignId('site_id');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['wfh_anchor_enforced', 'wfh_anchor_limit_m']);
        });
    }
};

