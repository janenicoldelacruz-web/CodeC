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
    Schema::create('class_schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_period_id')->constrained('academic_periods')->onDelete('cascade');
        $table->foreignId('section_id')->constrained('academic_sections')->onDelete('cascade');
        $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
        $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
        $table->string('day_of_week', 20);
        $table->time('start_time');
        $table->time('end_time');
        $table->string('room', 50)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
