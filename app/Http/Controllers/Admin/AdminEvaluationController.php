<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminEvaluationController extends Controller
{
    // Main Evaluations View
    public function index()
    {
        $activeSchoolYear = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_school_year')->value('value') ?? '2025-2026') 
            : '2025-2026';

        $activeSemester = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_semester')->value('value') ?? '1st Semester') 
            : '1st Semester';

        return view('admin.evaluations.index', compact('activeSchoolYear', 'activeSemester'));
    }

    // Evaluation Periods Management View
    public function periods()
    {
        $activeSchoolYear = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_school_year')->value('value') ?? '2025-2026') 
            : '2025-2026';

        $activeSemester = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_semester')->value('value') ?? '1st Semester') 
            : '1st Semester';

        return view('admin.evaluations.periods', compact('activeSchoolYear', 'activeSemester'));
    }

    // Evaluation Results View
    public function results()
    {
        $activeSchoolYear = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_school_year')->value('value') ?? '2025-2026') 
            : '2025-2026';

        $activeSemester = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_semester')->value('value') ?? '1st Semester') 
            : '1st Semester';

        return view('admin.evaluations.results', compact('activeSchoolYear', 'activeSemester'));
    }
}