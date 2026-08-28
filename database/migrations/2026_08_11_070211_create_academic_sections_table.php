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
    Schema::create('academic_sections', function (Blueprint $table) {
        $table->id();
        $table->string('grade_level', 50);
        $table->string('section_name', 100);
        $table->string('strand', 50)->nullable();
        $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_sections');
    }
};
