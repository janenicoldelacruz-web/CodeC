<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.schedules');
        } elseif ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        // Fallback default
        return view('dashboard');
    }

    // =========================================================================
    // Student Analytics & Filtering Screen Action
    // =========================================================================
    public function studentAnalytics(Request $request)
    {
        $query = User::where('role_id', 3);

        // Filter by Strand / Track
        if ($request->filled('strand')) {
            $query->where('strand', $request->strand);
        }

        // Filter by Section
        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        $students = $query->latest('id')->paginate(15);

        // Live Database Demographics Counts
        $maleCount = User::where('role_id', 3)->where('gender', 'Male')->count();
        $femaleCount = User::where('role_id', 3)->where('gender', 'Female')->count();

        // Live Database Section Populations
        $sectionPopulations = User::where('role_id', 3)
            ->whereNotNull('section')
            ->select('section', DB::raw('count(*) as total'))
            ->groupBy('section')
            ->pluck('total', 'section')
            ->toArray();

        // Live Database Distinct Sections for Dropdown Options
        $sections = User::where('role_id', 3)->whereNotNull('section')->distinct()->pluck('section')->toArray();

        return view('admin.students_analytics', compact(
            'students', 
            'maleCount', 
            'femaleCount', 
            'sectionPopulations',
            'sections'
        ));
    }
}