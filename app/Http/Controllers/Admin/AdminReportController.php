<?php

namespace App\Http\Controllers\Admin; // <-- Must include \Admin

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminReportController extends Controller
{
    public function index()
    {
        $totalStudents = Schema::hasTable('users') ? User::where('role_id', 3)->count() : 0;
        $totalFaculty  = Schema::hasTable('users') ? User::where('role_id', 2)->count() : 0;
        $totalLogs     = Schema::hasTable('attendance_logs') ? DB::table('attendance_logs')->count() : 0;
        $totalEvals    = Schema::hasTable('evaluation_submissions') ? DB::table('evaluation_submissions')->count() : 0;

        return view('admin.reports', compact('totalStudents', 'totalFaculty', 'totalLogs', 'totalEvals'));
    }

    public function attendance() { return view('admin.reports.attendance'); }
    public function sf2() { return view('admin.reports.sf2'); }
    public function evaluation() { return view('admin.reports.evaluation'); }
    public function users() { return view('admin.reports.users'); }
}