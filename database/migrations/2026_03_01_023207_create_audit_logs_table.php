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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // 'create', 'update', 'delete', etc.
            $table->string('model_type'); // e.g., 'User', 'Site', 'AttendanceSession'
            $table->unsignedBigInteger('model_id')->nullable(); // which record was affected
            $table->json('old_values')->nullable(); // previous values
            $table->json('new_values')->nullable(); // new values
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id','action']);
            $table->index(['user_id','model_type']);
            $table->index(['user_id','created_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
