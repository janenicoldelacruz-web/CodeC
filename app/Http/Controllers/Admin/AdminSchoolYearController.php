<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class AdminSchoolYearController extends Controller
{
public function index()
{
    $activeSchoolYear = DB::table('settings')->where('key', 'active_school_year')->value('value') ?? '2025-2026';
    $activeSemester = DB::table('settings')->where('key', 'active_semester')->value('value') ?? '1st Semester';
    $totalLogs = Schema::hasTable('attendance_logs') ? DB::table('attendance_logs')->count() : 0;

    return view('admin.school-year.index', compact('activeSchoolYear', 'activeSemester', 'totalLogs'));
}

    public function update(Request $request)
{
    if ($request->filled('admin_password')) {
        if ($request->admin_password !== auth()->user()->password) {
            return back()->with('error', 'Incorrect admin password. Changes not saved.');
        }
    }

    $request->validate([
        'academic_year' => 'required|string|max:50',
        'semester' => 'required|string|max:100'
    ]);

    if (Schema::hasTable('settings')) {
        DB::table('settings')->updateOrInsert(
            ['key' => 'active_school_year'],
            ['value' => $request->academic_year, 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'active_semester'],
            ['value' => $request->semester, 'updated_at' => now()]
        );
    }

    // TEST THIS: Does this redirect successfully trigger?
    return back()->with('success', 'Active school year and semester successfully updated.');
}

    public function reset()
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->whereIn('key', ['active_school_year', 'active_semester'])->delete();
        }

        return back()->with('success', 'School year configuration reset to default.');
    }
}