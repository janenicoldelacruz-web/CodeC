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
        return view('admin.dashboard.index', compact(
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
    public function showAnalyticsReport($type)
    {
        $reportTitle = match($type) {
            'students' => 'Total Students Analytics & Demographics',
            'attendance' => 'Subject Attendance & Gate Tap Summary Report',
            'evaluation' => 'Faculty Evaluation Performance Metrics',
            'sms' => 'Parent SMS Gateway Delivery Logs',
            default => 'Institutional Analytics Report'
        };

        // Fetch auxiliary data if needed depending on $type
        $totalStudents = User::where('role_id', 3)->count();
        $totalFaculty  = User::where('role_id', 2)->count();

        return view('admin.dashboard.analytics-report', compact('type', 'reportTitle', 'totalStudents', 'totalFaculty'));
    }
    public function evaluations(Request $request)
    {
        $averageScore = 0.0;
        $totalEvaluations = 0;
        $totalFaculty = User::where('role_id', 2)->count();
        $totalStudents = User::where('role_id', 3)->count();
        
        $evalProgress = 0;
        if (Schema::hasTable('evaluation_submissions') && $totalStudents > 0) {
            $evalCols = Schema::getColumnListing('evaluation_submissions');
            $evaluatorKey = in_array('student_id', $evalCols) ? 'student_id' : (in_array('user_id', $evalCols) ? 'user_id' : null);

            $studentsSubmitted = $evaluatorKey 
                ? DB::table('evaluation_submissions')->distinct($evaluatorKey)->count($evaluatorKey)
                : DB::table('evaluation_submissions')->count();

            $evalProgress = min(100, round(($studentsSubmitted / $totalStudents) * 100));
        }

        if (Schema::hasTable('evaluation_submissions')) {
            $evalCols = Schema::getColumnListing('evaluation_submissions');
            $ratingCol = in_array('overall_rating', $evalCols) ? 'overall_rating' : (in_array('rating', $evalCols) ? 'rating' : (in_array('score', $evalCols) ? 'score' : null));

            $totalEvaluations = DB::table('evaluation_submissions')->count();

            if ($ratingCol && $totalEvaluations > 0) {
                $averageScore = (float) DB::table('evaluation_submissions')->avg($ratingCol);
            }
        }

        // 1. Student Evaluations Query
        $studentEvaluations = collect();
        if (Schema::hasTable('evaluation_submissions')) {
            $evalCols = Schema::getColumnListing('evaluation_submissions');
            $teacherKey = in_array('teacher_id', $evalCols) ? 'teacher_id' : (in_array('user_id', $evalCols) ? 'user_id' : null);
            $studentKey = in_array('student_id', $evalCols) ? 'student_id' : 'user_id';

            $query = DB::table('evaluation_submissions');
            if (in_array('evaluation_type', $evalCols)) {
                $query->where('evaluation_type', 'student');
            }

            $query->leftJoin('users as teachers', 'evaluation_submissions.' . $teacherKey, '=', 'teachers.id')
                  ->leftJoin('users as students', 'evaluation_submissions.' . $studentKey, '=', 'students.id')
                  ->select(
                      'evaluation_submissions.*',
                      'teachers.first_name as teacher_first_name',
                      'teachers.last_name as teacher_last_name',
                      'teachers.strand as teacher_strand',
                      'students.first_name as student_first_name',
                      'students.last_name as student_last_name',
                      'students.grade_level as student_grade_level',
                      'students.track as student_track',
                      'students.section as student_section'
                  );

            if ($request->filled('section')) {
                $query->where('students.section', $request->section);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('teachers.first_name', 'like', "%{$search}%")
                      ->orWhere('teachers.last_name', 'like', "%{$search}%")
                      ->orWhere('students.first_name', 'like', "%{$search}%")
                      ->orWhere('students.last_name', 'like', "%{$search}%");
                });
            }

            $studentEvaluations = $query->latest('evaluation_submissions.id')->paginate(10, ['*'], 'student_page')->withQueryString();
        }

        // 2. Peer Evaluations Query
        $peerEvaluations = collect();
        if (Schema::hasTable('evaluation_submissions')) {
            $evalCols = Schema::getColumnListing('evaluation_submissions');
            $teacherKey = in_array('teacher_id', $evalCols) ? 'teacher_id' : (in_array('user_id', $evalCols) ? 'user_id' : null);
            $evaluatorKey = in_array('evaluator_id', $evalCols) ? 'evaluator_id' : (in_array('student_id', $evalCols) ? 'student_id' : null);

            $query = DB::table('evaluation_submissions');
            if (in_array('evaluation_type', $evalCols)) {
                $query->where('evaluation_type', 'peer');
            }

            $query->leftJoin('users as teachers', 'evaluation_submissions.' . $teacherKey, '=', 'teachers.id');
            
            if ($evaluatorKey) {
                $query->leftJoin('users as peers', 'evaluation_submissions.' . $evaluatorKey, '=', 'peers.id')
                      ->addSelect('peers.first_name as peer_first_name', 'peers.last_name as peer_last_name');
            }

            $query->select(
                'evaluation_submissions.*',
                'teachers.first_name as teacher_first_name',
                'teachers.last_name as teacher_last_name',
                'teachers.strand as teacher_strand'
            );

            $peerEvaluations = $query->latest('evaluation_submissions.id')->paginate(10, ['*'], 'peer_page')->withQueryString();
        }

        // 3. Self Evaluations Query
        $selfEvaluations = collect();
        if (Schema::hasTable('evaluation_submissions')) {
            $evalCols = Schema::getColumnListing('evaluation_submissions');
            $teacherKey = in_array('teacher_id', $evalCols) ? 'teacher_id' : (in_array('user_id', $evalCols) ? 'user_id' : null);

            $query = DB::table('evaluation_submissions');
            if (in_array('evaluation_type', $evalCols)) {
                $query->where('evaluation_type', 'self');
            }

            if ($teacherKey) {
                $query->leftJoin('users as teachers', 'evaluation_submissions.' . $teacherKey, '=', 'teachers.id')
                      ->select(
                          'evaluation_submissions.*',
                          'teachers.first_name as teacher_first_name',
                          'teachers.last_name as teacher_last_name',
                          'teachers.strand as teacher_strand'
                      );
            }

            $selfEvaluations = $query->latest('evaluation_submissions.id')->paginate(10, ['*'], 'self_page')->withQueryString();
        }

        $topRatedFaculty = collect();
        $sections = User::where('role_id', 3)->whereNotNull('section')->distinct()->pluck('section');

        return view('admin.evaluations.index', compact(
            'averageScore',
            'totalEvaluations',
            'totalFaculty',
            'evalProgress',
            'studentEvaluations',
            'peerEvaluations',
            'selfEvaluations',
            'topRatedFaculty',
            'sections'
        ));
    }
}