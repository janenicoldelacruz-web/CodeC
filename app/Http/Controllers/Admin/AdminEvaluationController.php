<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class AdminEvaluationController extends Controller
{
    public function index(Request $request)
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

    public function toggleStatus(Request $request)
    {
        $isOpen = $request->input('status') === true || $request->input('status') === 'true';
        Cache::forever('evaluations_open', $isOpen);
        return response()->json(['success' => true, 'is_open' => $isOpen]);
    }

    public function periods()
    {
        $periods = Schema::hasTable('academic_periods') 
            ? DB::table('academic_periods')->orderBy('id', 'desc')->get() 
            : collect([
                (object)['id' => 1, 'semester' => '1st Semester', 'term' => 'Term 1', 'school_year' => '2027-2028', 'is_active' => 1, 'start_date' => '2027-08-15', 'end_date' => '2027-10-30'],
                (object)['id' => 2, 'semester' => '1st Semester', 'term' => 'Term 2', 'school_year' => '2027-2028', 'is_active' => 0, 'start_date' => '2027-11-05', 'end_date' => '2028-01-25'],
                (object)['id' => 3, 'semester' => '2nd Semester', 'term' => 'Term 3', 'school_year' => '2027-2028', 'is_active' => 0, 'start_date' => '2028-02-10', 'end_date' => '2028-05-30'],
            ]);

        $criteria = [
            'mastery' => [
                'title' => 'Instructional Competence & Subject Mastery',
                'weight' => '30%',
                'questions' => [
                    'Demonstrates in-depth mastery of the subject matter and curriculum competencies.',
                    'Explains difficult concepts clearly with real-life application and context.',
                    'Integrates updated learning materials and DepEd-aligned competencies effectively.'
                ]
            ],
            'management' => [
                'title' => 'Classroom Management & Learning Environment',
                'weight' => '25%',
                'questions' => [
                    'Maintains discipline, mutual respect, and an engaging classroom atmosphere.',
                    'Maximizes instructional time and minimizes idle classroom activities.',
                    'Treats students equitably regardless of academic standing and background.'
                ]
            ],
            'methodology' => [
                'title' => 'Teaching Methodology & Learning Assessment',
                'weight' => '20%',
                'questions' => [
                    'Utilizes varied learning methodologies, multimedia, and instructional activities.',
                    'Provides constructive feedback on student assessments and performance tasks promptly.',
                    'Encourages critical thinking, inquiry, and interactive student participation.'
                ]
            ],
            'qualities' => [
                'title' => 'Professional Character & Student Relations',
                'weight' => '25%',
                'questions' => [
                    'Displays punctuality in class attendance and schedule adherence.',
                    'Models moral integrity, professional decorum, and approachable demeanor.',
                    'Shows genuine concern and readiness to provide student academic guidance.'
                ]
            ]
        ];

        return view('admin.evaluations.periods', compact('periods', 'criteria'));
    }

    public function results(Request $request)
    {
        $facultyQuery = User::where('role_id', 2);

        if ($request->filled('search')) {
            $search = $request->search;
            $facultyQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $facultyList = $facultyQuery->orderBy('last_name', 'asc')->paginate(10)->withQueryString();

        $averageScore = 0.0;
        $totalSubmissions = 0;
        if (Schema::hasTable('evaluation_submissions')) {
            $totalSubmissions = DB::table('evaluation_submissions')->count();
            if ($totalSubmissions > 0) {
                $evalCols = Schema::getColumnListing('evaluation_submissions');
                $ratingCol = in_array('overall_rating', $evalCols) ? 'overall_rating' : (in_array('rating', $evalCols) ? 'rating' : (in_array('score', $evalCols) ? 'score' : null));
                if ($ratingCol) {
                    $averageScore = (float) DB::table('evaluation_submissions')->avg($ratingCol);
                }
            }
        }

        return view('admin.evaluations.results', compact('facultyList', 'averageScore', 'totalSubmissions'));
    }
}