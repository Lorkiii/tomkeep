<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Base authentication and identity schema used by the application.
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Core users table including auth fields and student profile metadata.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->nullable()->unique();
            $table->string('first_name', 30)->nullable();
            $table->string('middle_name', 30)->nullable();
            $table->string('last_name', 30)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('student_code', 20)->nullable()->unique();
            $table->string('position', 120)->default('OJT Trainee');
            $table->string('contact_number', 11)->nullable();
            $table->json('address')->nullable();
            $table->string('course',255)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('school_attended',255)->nullable();
            $table->unsignedInteger('number_of_hours')->default(0);
            $table->boolean('profile_completed')->default(false);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'student'])->default('student');

            // Registration lifecycle
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();

            // remember token
            $table->rememberToken();

            // Runtime presence (timekeeping)
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_seen_at')->nullable();

            // created_at / updated_at with CURRENT_TIMESTAMP behavior
            $table->timestamp('created_at')->useCurrent();
            // useCurrentOnUpdate exists in modern Laravel versions; this sets ON UPDATE CURRENT_TIMESTAMP
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            // table indexes
            // Helpful indexes
            $table->index(['role', 'status']);

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};