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

class TeacherDashboardController extends Controller
{


// SCHEDULE MATRIX METHOD (Main Landing Page)
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

        // 1. Fetch the specific class schedule/subject assigned to this teacher
        $schedule = ClassSchedule::where('id', $scheduleId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        // 2. Fetch students enrolled in this section/strand
        // (Adjust the query based on how your students are linked to sections/strands)
        $students = User::where('role_id', 3) // Assuming role_id 3 is student
            ->when(Schema::hasColumn('users', 'section'), function($q) use ($schedule) {
                $q->where('section', $schedule->section);
            })
            ->get();

        return view('teacher.class-list', compact('teacher', 'schedule', 'students'));
    }
}