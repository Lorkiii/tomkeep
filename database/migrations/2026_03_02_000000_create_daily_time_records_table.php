<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('attendance_mode', 20)->nullable();
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->decimal('time_in_latitude', 10, 7)->nullable();
            $table->decimal('time_in_longitude', 10, 7)->nullable();
            $table->time('lunch_out')->nullable();
            $table->time('lunch_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('time_out_latitude', 10, 7)->nullable();
            $table->decimal('time_out_longitude', 10, 7)->nullable();
            $table->unsignedInteger('wfh_movement_limit_m')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['user_id','date']);
            $table->index(['attendance_mode', 'date']);
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_time_records');
    }
};
