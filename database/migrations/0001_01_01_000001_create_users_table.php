<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_number', 50)->nullable()->unique();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedTinyInteger('gender')->nullable(); // 1 = Male, 2 = Female
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone_number')->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->string('strand')->nullable();
            $table->string('section', 50)->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // PERMANENT ADMIN SEED (NATURAL CASING & PLAIN TEXT PASSWORD)
        DB::table('users')->insert([
            'id_number'           => 'ADM-2026-001',
            'role_id'             => 1,
            'first_name'          => 'System',
            'last_name'           => 'Administrator',
            'gender'              => 1,
            'email'               => 'admin@siatrack.edu.ph',
            'phone_number'        => '09171234567',
            'parent_name'         => null,
            'parent_phone_number' => null,
            'grade_level'         => null,
            'strand'              => null, // Pinalitan mula 'track' patungong 'strand'
            'section'             => null,
            'password'            => 'AdminPass2026!',
            'is_active'           => true,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // 2. Password Reset Tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Sessions
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