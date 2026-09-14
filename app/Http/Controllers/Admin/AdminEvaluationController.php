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

        $periods = Schema::hasTable('academic_periods') 
            ? DB::table('academic_periods')->orderBy('id', 'desc')->get() 
            : collect();

        $criteria = [
            'teaching_effectiveness' => [
                'title' => 'Teaching Effectiveness & Instruction',
                'weight' => '40%',
                'questions' => [
                    'Demonstrates mastery of the subject matter and explains concepts clearly.',
                    'Uses effective teaching strategies and instructional materials.',
                    'Engages students actively in the learning process.'
                ]
            ],
            'classroom_management' => [
                'title' => 'Classroom Management & Organization',
                'weight' => '30%',
                'questions' => [
                    'Starts and ends classes promptly as scheduled.',
                    'Maintains a disciplined, orderly, and conducive learning environment.',
                    'Manages classroom time efficiently.'
                ]
            ],
            'student_relations' => [
                'title' => 'Student Relations & Professionalism',
                'weight' => '30%',
                'questions' => [
                    'Shows fairness, respect, and concern for students.',
                    'Is approachable and available for consultations or academic guidance.',
                    'Exhibits professional conduct and sets a good example.'
                ]
            ]
        ];

        return view('admin.evaluations.index', compact('activeSchoolYear', 'activeSemester', 'periods', 'criteria'));
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

        $periods = Schema::hasTable('academic_periods') 
            ? DB::table('academic_periods')->orderBy('id', 'desc')->get() 
            : collect();

        $criteria = [
            'teaching_effectiveness' => [
                'title' => 'Teaching Effectiveness & Instruction',
                'weight' => '40%',
                'questions' => [
                    'Demonstrates mastery of the subject matter and explains concepts clearly.',
                    'Uses effective teaching strategies and instructional materials.',
                    'Engages students actively in the learning process.'
                ]
            ],
            'classroom_management' => [
                'title' => 'Classroom Management & Organization',
                'weight' => '30%',
                'questions' => [
                    'Starts and ends classes promptly as scheduled.',
                    'Maintains a disciplined, orderly, and conducive learning environment.',
                    'Manages classroom time efficiently.'
                ]
            ],
            'student_relations' => [
                'title' => 'Student Relations & Professionalism',
                'weight' => '30%',
                'questions' => [
                    'Shows fairness, respect, and concern for students.',
                    'Is approachable and available for consultations or academic guidance.',
                    'Exhibits professional conduct and sets a good example.'
                ]
            ]
        ];

        return view('admin.evaluations.periods', compact('activeSchoolYear', 'activeSemester', 'periods', 'criteria'));
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

    // Toggle Evaluation Status
    public function toggleStatus(Request $request)
    {
        if (Schema::hasTable('system_settings')) {
            $current = DB::table('system_settings')->where('key', 'evaluation_status')->value('value');
            $newStatus = in_array(strtolower((string)$current), ['open', 'active', '1', 'true']) ? 'closed' : 'open';
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'evaluation_status'],
                ['value' => $newStatus, 'updated_at' => now()]
            );
        } elseif (Schema::hasTable('settings')) {
            $current = DB::table('settings')->where('key', 'evaluation_status')->value('value');
            $newStatus = in_array(strtolower((string)$current), ['open', 'active', '1', 'true']) ? 'closed' : 'open';
            DB::table('settings')->updateOrInsert(
                ['key' => 'evaluation_status'],
                ['value' => $newStatus, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Faculty evaluation status successfully toggled.');
    }
}