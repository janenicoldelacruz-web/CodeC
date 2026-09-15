<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->enum('status', ['ON-TIME', 'LATE'])->default('ON-TIME');
            $table->string('sms_status', 50)->default('PENDING');
            
            // Idinagdag natin ang mga ito para sa Academic Year History & Reset:
            $table->string('academic_year')->nullable();
            $table->string('semester')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'attendance_date']);
            $table->index(['academic_year', 'semester']); // Index para mabilis ang filtering
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};