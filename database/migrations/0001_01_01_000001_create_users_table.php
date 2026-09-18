<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_number', 50)->nullable()->unique();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20)->nullable(); // Changed to string to accept 'Male' or 'Female' from forms
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone_number')->nullable();
            $table->unsignedTinyInteger('grade_level')->nullable();
            $table->string('strand')->nullable();
            $table->string('section')->nullable(); // Changed to string to support section names/numbers cleanly
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            'id_number'           => 'ADM-2026-001',
            'role_id'             => 1,
            'first_name'          => 'System',
            'last_name'           => 'Administrator',
            'gender'              => 'Male', // Updated to string value
            'email'               => 'admin@siatrack.edu.ph',
            'phone_number'        => '09171234567',
            'parent_name'         => null,
            'parent_phone_number' => null,
            'grade_level'         => null,
            'strand'              => null,
            'section'             => null,
            'password'            => 'AdminPass2026!',
            'is_active'           => true,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

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

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};