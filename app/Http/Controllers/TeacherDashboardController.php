<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    /**
     * 1. Class Attendance View (Figure 17)
     */
    public function index(Request $request)
    {
        $selectedStrand = $request->query('strand');
        $search = $request->query('search');

        $query = User::where('role_id', 3);

        if (!empty($selectedStrand)) {
            $query->where('strand', $selectedStrand);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->get();

        $attendanceLogs = $students->map(function ($student, $index) {
            $log = null;

            // Ligtas na pag-query gamit ang try-catch
            try {
                if (Schema::hasTable('attendance_logs')) {
                    $attendanceQuery = DB::table('attendance_logs');

                    if (Schema::hasColumn('attendance_logs', 'student_id')) {
                        $attendanceQuery->where('student_id', $student->id);
                    } elseif (Schema::hasColumn('attendance_logs', 'user_id')) {
                        $attendanceQuery->where('user_id', $student->id);
                    } else {
                        $attendanceQuery = null;
                    }

                    if ($attendanceQuery) {
                        $log = $attendanceQuery->latest()->first();
                    }
                }
            } catch (\Throwable $e) {
                $log = null;
            }

            if ($log) {
                $timeField = $log->time_in ?? $log->created_at ?? now();
                $timeIn = Carbon::parse($timeField)->format('h:i A');
                $status = strtoupper($log->status ?? 'ON-TIME');
            } else {
                // Dynamic preview base sa sample data
                $statuses = ['ON-TIME', 'LATE', 'ABSENT', 'ON-TIME'];
                $times    = ['8:05 AM', '8:30 AM', '9:00 AM', '8:10 AM'];
                $status   = $statuses[$index % count($statuses)];
                $timeIn   = ($status === 'ABSENT') ? '--:--' : $times[$index % count($times)];
            }

            return (object) [
                'id'        => $student->id,
                'id_number' => $student->id_number ?? '00' . (230 + $student->id),
                'name'      => $student->first_name . ' ' . $student->last_name,
                'strand'    => $student->strand ?? 'N/A',
                'time_in'   => $timeIn,
                'status'    => $status,
            ];
        });

        $activeClasses = [
            (object) [
                'title'   => 'Programming 1 - Grade 11 - B',
                'subject' => 'Information & Communications Technology',
                'time'    => '8:00 AM - 9:30 AM',
                'room'    => 'Computer Lab 1',
            ],
            (object) [
                'title'   => 'Empowerment Technologies - Grade 12 - A',
                'subject' => 'Applied Subject Area',
                'time'    => '10:00 AM - 11:30 AM',
                'room'    => 'Room 204',
            ],
        ];

        return view('teacher.dashboard', compact('attendanceLogs', 'activeClasses', 'selectedStrand', 'search'));
    }

    /**
     * 2. Absence Reporting (Figure 18)
     */
    public function absenceReporting(Request $request)
    {
        $selectedDate = $request->query('date', Carbon::today()->format('Y-m-d'));
        $search = $request->query('search');

        $studentsQuery = User::where('role_id', 3);
        if (!empty($search)) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $allStudents = $studentsQuery->get();

        $totalStudents = $allStudents->count() > 0 ? $allStudents->count() : 40;

        $presentStudents = collect();
        $waitingForScan = collect();

        foreach ($allStudents as $index => $student) {
            if ($index % 5 !== 4) {
                $presentStudents->push((object)[
                    'id'        => $student->id,
                    'id_number' => $student->id_number ?? '00' . (230 + $student->id),
                    'name'      => $student->first_name . ' ' . $student->last_name,
                    'time_in'   => '8:05 AM',
                    'time_out'  => '10:05 AM',
                ]);
            } else {
                $waitingForScan->push((object)[
                    'id'        => $student->id,
                    'id_number' => $student->id_number ?? '00' . (230 + $student->id),
                    'name'      => $student->first_name . ' ' . $student->last_name,
                ]);
            }
        }

        $absentCount = $waitingForScan->count() > 0 ? $waitingForScan->count() : 5;

        return view('teacher.absence-reporting', compact(
            'totalStudents',
            'absentCount',
            'presentStudents',
            'waitingForScan',
            'allStudents',
            'selectedDate',
            'search'
        ));
    }

    /**
     * 3. Evaluation Report View (Figure 19)
     */
    public function evaluationReport()
    {
        $performanceMetrics = [
            (object) ['criteria' => 'Mastery of Subject Matter', 'score' => 4.8],
            (object) ['criteria' => 'Communication Skills', 'score' => 4.9],
            (object) ['criteria' => 'Classroom Management', 'score' => 4.7],
            (object) ['criteria' => 'Teaching Methodology & Evaluation', 'score' => 4.9],
            (object) ['criteria' => 'Professional and Personal Qualities', 'score' => 5.0],
        ];

        $overallAverage = collect($performanceMetrics)->avg('score');
        $totalEvaluations = 350;

        $studentFeedbacks = [
            (object) [
                'tone'     => 'Highly Positive',
                'feedback' => 'The teacher explains the lessons very clearly and provides real-world examples without just reading from the slides.',
            ],
            (object) [
                'tone'     => 'Highly Positive',
                'feedback' => 'Very approachable during consultations and always begins and ends class on time with structured discussions.',
            ],
            (object) [
                'tone'     => 'Positive',
                'feedback' => 'Gives engaging hands-on programming activities and encourages critical thinking among all students.',
            ],
            (object) [
                'tone'     => 'Highly Positive',
                'feedback' => 'Consistently treats every student fairly and makes the learning environment encouraging and enjoyable.',
            ],
        ];

        return view('teacher.evaluation-report', compact(
            'overallAverage',
            'totalEvaluations',
            'performanceMetrics',
            'studentFeedbacks'
        ));
    }
}