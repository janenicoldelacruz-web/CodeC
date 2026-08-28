<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('gender')->nullable(); // 1 = Male, 2 = Female
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone_number')->nullable();
            $table->unsignedTinyInteger('grade_level')->nullable(); // 11 = Grade 11, 12 = Grade 12
            $table->unsignedTinyInteger('track')->nullable(); // 1 = Academic Track, 2 = Technical-Professional
            $table->unsignedTinyInteger('section')->nullable(); // 1 = Crystal, 2 = Turquoise, 3 = Amber, 4 = Pearl
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

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