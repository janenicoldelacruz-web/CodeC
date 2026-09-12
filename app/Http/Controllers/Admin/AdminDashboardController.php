<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. Real User Counts from Database
        $totalStudents = User::where('role_id', 3)->count();
        $totalFaculty  = User::where('role_id', 2)->count();
        $totalAdmins   = User::where('role_id', 1)->count();
        $totalUsers    = User::count();

        // 2. Real Today Attendance Calculation
        $presentTodayCount = 0;
        $lateTodayCount = 0;
        $onTimeTodayCount = 0;
        $recentTaps = collect();

        if (Schema::hasTable('attendance_logs')) {
            $cols = Schema::getColumnListing('attendance_logs');
            $userCols = Schema::getColumnListing('users');
            
            $foreignKey = in_array('student_id', $cols) ? 'student_id' : (in_array('user_id', $cols) ? 'user_id' : null);
            $dateCol = in_array('attendance_date', $cols) ? 'attendance_date' : (in_array('date', $cols) ? 'date' : (in_array('created_at', $cols) ? 'created_at' : null));

            $todayQuery = DB::table('attendance_logs');
            if ($dateCol) {
                $todayQuery->whereDate('attendance_logs.' . $dateCol, $today);
            }

            if ($foreignKey) {
                $presentTodayCount = (clone $todayQuery)->distinct($foreignKey)->count($foreignKey);
            } else {
                $presentTodayCount = (clone $todayQuery)->count();
            }

            if (in_array('status', $cols)) {
                $lateTodayCount = (clone $todayQuery)->where('status', 'LATE')->count();
                $onTimeTodayCount = (clone $todayQuery)->where('status', 'ON-TIME')->count();
            }

            if ($foreignKey) {
                $selectFields = [
                    'users.first_name',
                    'users.last_name',
                    'users.id_number',
                    DB::raw(in_array('time_in', $cols) ? 'attendance_logs.time_in' : 'attendance_logs.created_at as time_in'),
                    DB::raw(in_array('status', $cols) ? 'attendance_logs.status' : "'ON-TIME' as status"),
                ];

                if (in_array('grade_level', $userCols)) $selectFields[] = 'users.grade_level';
                if (in_array('track', $userCols)) $selectFields[] = 'users.track';
                if (in_array('section', $userCols)) $selectFields[] = 'users.section';

                $streamQuery = DB::table('attendance_logs')
                    ->join('users', 'attendance_logs.' . $foreignKey, '=', 'users.id')
                    ->select($selectFields);

                if ($dateCol) {
                    $streamQuery->whereDate('attendance_logs.' . $dateCol, $today);
                }

                $recentTaps = $streamQuery->latest('attendance_logs.created_at')->take(10)->get();
            }
        }

        $attendanceRate = $totalStudents > 0 
            ? round(($presentTodayCount / $totalStudents) * 100, 1) . '%' 
            : '0%';

        $evalProgress = '0%';
        if (Schema::hasTable('evaluation_submissions') && $totalStudents > 0) {
            $cols = Schema::getColumnListing('evaluation_submissions');
            $evaluatorKey = in_array('student_id', $cols) ? 'student_id' : (in_array('user_id', $cols) ? 'user_id' : null);

            $studentsSubmitted = $evaluatorKey 
                ? DB::table('evaluation_submissions')->distinct($evaluatorKey)->count($evaluatorKey)
                : DB::table('evaluation_submissions')->count();

            $evalProgress = round(($studentsSubmitted / $totalStudents) * 100, 1) . '%';
        }

        $activeSMS = 0;
        if (Schema::hasTable('sms_logs')) {
            $smsCols = Schema::getColumnListing('sms_logs');
            $smsDateCol = in_array('created_at', $smsCols) ? 'created_at' : (in_array('date', $smsCols) ? 'date' : null);
            if ($smsDateCol) {
                $activeSMS = DB::table('sms_logs')->whereDate($smsDateCol, $today)->count();
            } else {
                $activeSMS = DB::table('sms_logs')->count();
            }
        }

        // Fetch the encoded school year from the database settings table
        $activeSchoolYear = null;
        if (Schema::hasTable('settings')) {
            $activeSchoolYear = DB::table('settings')->where('key', 'active_school_year')->value('value');
        }

        // Fallback message if nothing has been encoded in the system yet
        if (!$activeSchoolYear) {
            $activeSchoolYear = 'Not Encoded';
        }

        // Return your modular dashboard index view with $activeSchoolYear included
        return view('admin.dashboard', compact(
            'totalStudents',
            'totalFaculty',
            'totalAdmins',
            'totalUsers',
            'presentTodayCount',
            'lateTodayCount',
            'onTimeTodayCount',
            'recentTaps',
            'attendanceRate',
            'evalProgress',
            'activeSMS',
            'activeSchoolYear'
        ));
    }

   public function showAnalyticsReport(Request $request, $type)
    {
        $reportTitle = match($type) {
            'students' => 'Total Students Analytics & Demographics',
            'attendance' => 'Subject Attendance & Gate Tap Summary Report',
            'evaluation' => 'Faculty Evaluation Performance Metrics',
            'sms' => 'Parent SMS Gateway Delivery Logs',
            default => 'Institutional Analytics Report'
        };

        $totalStudents = User::where('role_id', 3)->count();
        $totalFaculty  = User::where('role_id', 2)->count();

        // Build query with Strand and Section filters (supporting both 'section' and 'section_id' columns)
        $query = User::where('role_id', 3);
        $userCols = Schema::hasTable('users') ? Schema::getColumnListing('users') : [];

        if ($request->filled('strand')) {
            $query->where('strand', $request->strand);
        }

        if ($request->filled('section')) {
            if (in_array('section', $userCols)) {
                $query->where('section', $request->section);
            } elseif (in_array('section_id', $userCols)) {
                $query->where('section_id', $request->section);
            }
        }

        $students = $query->latest('id')->paginate(15)->withQueryString();

        // Calculate metrics for charts based on the filtered scope
        $maleCount = User::where('role_id', 3)
            ->when($request->filled('strand'), fn($q) => $q->where('strand', $request->strand))
            ->when($request->filled('section'), fn($q) => in_array('section', $userCols) ? $q->where('section', $request->section) : $q->where('section_id', $request->section))
            ->where('gender', 'Male')
            ->count();
            
        $femaleCount = User::where('role_id', 3)
            ->when($request->filled('strand'), fn($q) => $q->where('strand', $request->strand))
            ->when($request->filled('section'), fn($q) => in_array('section', $userCols) ? $q->where('section', $request->section) : $q->where('section_id', $request->section))
            ->where('gender', 'Female')
            ->count();
        
        $sectionCol = in_array('section', $userCols) ? 'section' : (in_array('section_id', $userCols) ? 'section_id' : null);

        $sectionPopulations = $sectionCol ? User::where('role_id', 3)
            ->when($request->filled('strand'), fn($q) => $q->where('strand', $request->strand))
            ->whereNotNull($sectionCol)
            ->select($sectionCol, DB::raw('count(*) as total'))
            ->groupBy($sectionCol)
            ->pluck('total', $sectionCol)
            ->toArray() : [];

        // Robustly fetch ALL available sections from sections table or users table
        $sections = collect();
        if (Schema::hasTable('sections')) {
            $secCols = Schema::getColumnListing('sections');
            $secNameCol = null;
            foreach (['name', 'section_name', 'title', 'section'] as $c) {
                if (in_array($c, $secCols)) { $secNameCol = $c; break; }
            }
            if ($secNameCol) {
                $sections = DB::table('sections')->orderBy($secNameCol)->pluck($secNameCol);
            } else {
                $sections = DB::table('sections')->pluck('id');
            }
        }

        if ($sections->isEmpty() && $sectionCol) {
            $sections = User::where('role_id', 3)
                ->whereNotNull($sectionCol)
                ->where($sectionCol, '!=', '')
                ->distinct()
                ->orderBy($sectionCol)
                ->pluck($sectionCol);
        }

        // Ultimate fallback if no sections are found in database tables yet
        if ($sections->isEmpty()) {
            $sections = collect([1, 2, 3, 4, 'Amber', 'Crystal', 'Pearl', 'Turquoise']);
        }

        return view('admin.dashboard.analytics-report', compact(
            'type', 
            'reportTitle', 
            'totalStudents', 
            'totalFaculty',
            'students',
            'maleCount',
            'femaleCount',
            'sectionPopulations',
            'sections'
        ));
    }
}