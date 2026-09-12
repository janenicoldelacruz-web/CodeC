<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academic_periods') && !Schema::hasColumn('academic_periods', 'is_active')) {
            Schema::table('academic_periods', function (Blueprint $table) {
                $table->boolean('is_active')->default(0)->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('academic_periods') && Schema::hasColumn('academic_periods', 'is_active')) {
            Schema::table('academic_periods', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
