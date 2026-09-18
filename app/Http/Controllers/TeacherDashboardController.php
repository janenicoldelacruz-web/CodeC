<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Idinagdag para sa DB queries ng evaluation

class TeacherDashboardController extends Controller
{
    // SCHEDULE MATRIX METHOD (Main Landing Page)
    public function schedule(Request $request)
    {
        $teacher = Auth::user();
        $search = trim((string) $request->query('search'));

        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];
        $teacherCol = in_array('teacher_id', $schedCols) ? 'teacher_id' : (in_array('user_id', $schedCols) ? 'user_id' : null);

        if (!Schema::hasTable('class_schedules') || !$teacherCol) {
            $emptyCollection = collect();
            return view('teacher.schedules', [
                'teacher' => $teacher,
                'mySchedules' => $emptyCollection,
                'schedules' => $emptyCollection,
                'search' => $search
            ]);
        }

        $query = ClassSchedule::with(['subjectRecord', 'academicSection'])
            ->where($teacherCol, $teacher->id);

        if ($search) {
            $query->where(function($q) use ($search, $schedCols) {
                if (in_array('subject_name', $schedCols)) {
                    $q->where('subject_name', 'like', "%{$search}%");
                } elseif (in_array('subject', $schedCols)) {
                    $q->orWhere('subject', 'like', "%{$search}%");
                }
                
                if (in_array('section', $schedCols)) {
                    $q->orWhere('section', 'like', "%{$search}%");
                }

                $q->orWhereHas('subjectRecord', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $mySchedules = $query->get();
        $schedules = $mySchedules; // Alias in case the view loops through $schedules
        return view('teacher.schedules', compact('teacher', 'mySchedules', 'schedules', 'search'));
    }
// MAIN DASHBOARD METHOD (Real Data Only)
    public function index()
    {
        $teacher = Auth::user();

        // 1. Bilangin ang kabuuang klase na hawak ng teacher
        $totalClasses = 0;
        $todaysClasses = collect([]);
        
        if (Schema::hasTable('class_schedules')) {
            $schedCols = Schema::getColumnListing('class_schedules');
            $teacherCol = in_array('teacher_id', $schedCols) ? 'teacher_id' : (in_array('user_id', $schedCols) ? 'user_id' : null);
            
            if ($teacherCol) {
                $totalClasses = ClassSchedule::where($teacherCol, $teacher->id)->count();
                
                // Kunin ang mga klase ngayong araw kung may 'day' column
                if (in_array('day', $schedCols)) {
                    $today = now()->format('l'); // Halimbawa: 'Monday'
                    
                    // Tinanggal natin ang orderBy('time_start') para hindi mag-error
                    $todaysClasses = ClassSchedule::where($teacherCol, $teacher->id)
                                        ->where('day', $today)
                                        ->get();
                } else {
                    // Fallback kung walang 'day' column, kunin na lang lahat ng klase
                    $todaysClasses = ClassSchedule::where($teacherCol, $teacher->id)->take(5)->get();
                }
            }
        }

        // 2. Bilangin ang kabuuang enrolled students sa system (role_id = 3)
        $totalStudents = Schema::hasTable('users') 
            ? User::where('role_id', 3)->count() 
            : 0;

        return view('teacher.dashboard', compact('teacher', 'totalClasses', 'totalStudents', 'todaysClasses'));
    }
    // PROFILE UPDATE METHOD (Handles the Edit Faculty Profile modal)
    public function updateProfile(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'first_name'   => ['required', 'string', 'max:255'],
            'last_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'id_number'    => ['required', 'string', 'max:255'],
            'gender'       => ['required', 'in:1,2'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'photo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $teacher->first_name   = $request->first_name;
        $teacher->last_name    = $request->last_name;
        $teacher->email        = $request->email;
        $teacher->id_number    = $request->id_number;
        $teacher->gender       = $request->gender;
        $teacher->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $path = $request->file('photo')->store('teacher-photos', 'public');
            $teacher->photo = $path;
        }

        $teacher->save();

        return redirect()->back()->with('success', 'Faculty profile updated successfully!');
    }

    // SCHOOL YEAR & SECTIONS METHOD (Real Data Only)
    public function schoolYears()
    {
        $teacher = Auth::user();

        // Kukunin lang ang totoong data mula sa database
        $schoolYears = Schema::hasTable('school_years') 
            ? DB::table('school_years')->orderBy('start_date', 'desc')->get() 
            : collect([]); 

        $sections = Schema::hasTable('sections') 
            ? DB::table('sections')->orderBy('name', 'asc')->get() 
            : collect([]);

        return view('teacher.school-years', compact('teacher', 'schoolYears', 'sections'));
    }

    // STUDENT DIRECTORY METHOD (Real Data Only)
    public function students()
    {
        $teacher = Auth::user();

        // Kukunin ang mga estudyante (role_id = 3) mula sa database
        $students = Schema::hasTable('users') 
            ? User::where('role_id', 3)->orderBy('last_name', 'asc')->get() 
            : collect([]);

        return view('teacher.students', compact('teacher', 'students'));
    }
// MESSAGE INBOX METHOD (Real Data Only)
    public function messages()
    {
        $teacher = Auth::user();

        $messages = collect([]);

        // I-check kung may 'messages' table sa database para iwas error
        if (Schema::hasTable('messages')) {
            $messages = DB::table('messages')
                // I-join natin sa users table para makuha ang pangalan ng nag-send
                ->leftJoin('users as senders', 'messages.sender_id', '=', 'senders.id')
                ->where('messages.receiver_id', $teacher->id)
                ->select('messages.*', 'senders.first_name', 'senders.last_name')
                ->orderBy('messages.created_at', 'desc')
                ->get();
        }

        return view('teacher.messages', compact('teacher', 'messages'));
    }
    // REPORT DASHBOARD METHOD (Real Data Only)
    public function reports()
    {
        $teacher = Auth::user();

        // Kukunin ang mga klase na hawak ng teacher bilang basehan ng mga reports
        $myClasses = collect([]);
        if (Schema::hasTable('class_schedules')) {
            $schedCols = Schema::getColumnListing('class_schedules');
            $teacherCol = in_array('teacher_id', $schedCols) ? 'teacher_id' : (in_array('user_id', $schedCols) ? 'user_id' : null);
            
            if ($teacherCol) {
                // Kunin lang ang mga klase ng naka-login na teacher
                $myClasses = ClassSchedule::where($teacherCol, $teacher->id)->get();
            }
        }

        return view('teacher.reports', compact('teacher', 'myClasses'));
    }
    public function attendance()
    {
        $teacher = Auth::user();
        return view('teacher.attendance', compact('teacher'));
    }

    public function evaluationReport()
    {
        return view('teacher.evaluation-report');
    }

    // CLASS LIST PER SUBJECT METHOD
    public function classList($scheduleId)
    {
        $teacher = Auth::user();

        $schedule = ClassSchedule::where('id', $scheduleId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $students = User::where('role_id', 3) // Assuming role_id 3 is student
            ->when(Schema::hasColumn('users', 'section'), function($q) use ($schedule) {
                $q->where('section', $schedule->section);
            })
            ->get();

        return view('teacher.class-list', compact('teacher', 'schedule', 'students'));
    }

    // ==========================================
    // FACULTY EVALUATION METHODS (Peer & Self)
    // ==========================================

    public function evaluationsIndex()
    {
        $teacher = Auth::user();
        
        // Kunin ang ibang guro para sa Peer Evaluation (huwag isama ang sarili)
        $peers = User::where('role_id', 2)->where('id', '!=', $teacher->id)->get();

        // Kunin ang evaluation questions para sa peer at self forms
        $peerQuestions = Schema::hasTable('evaluation_questions') 
            ? DB::table('evaluation_questions')->where('form_type', 'peer')->get() 
            : collect([]);
            
        $selfQuestions = Schema::hasTable('evaluation_questions') 
            ? DB::table('evaluation_questions')->where('form_type', 'self')->get() 
            : collect([]);

        return view('teacher.evaluations.index', compact('teacher', 'peers', 'peerQuestions', 'selfQuestions'));
    }

    public function storePeerEvaluation(Request $request)
    {
        $request->validate([
            'evaluatee_id' => 'required|exists:users,id',
            'ratings'      => 'required|array',
        ]);

        $scores = array_values($request->ratings);
        $averageScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;

        DB::table('peer_evaluations')->insert([
            'evaluator_id'  => Auth::id(),
            'evaluatee_id'  => $request->evaluatee_id,
            'average_score' => $averageScore,
            'comments'      => $request->input('comments'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Peer evaluation submitted successfully!');
    }

    public function storeSelfEvaluation(Request $request)
    {
        $request->validate([
            'ratings' => 'required|array',
        ]);

        $scores = array_values($request->ratings);
        $averageScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;

        DB::table('self_evaluations')->insert([
            'teacher_id'    => Auth::id(),
            'average_score' => $averageScore,
            'comments'      => $request->input('comments'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return back()->with('success', 'Self-evaluation submitted successfully!');
    }
}