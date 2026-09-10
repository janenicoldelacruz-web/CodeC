<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\NfcAttendanceController;

/*
|--------------------------------------------------------------------------
| Public & Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('welcome');
Route::get('/login', function () { return redirect()->route('login.portal', ['role' => 'admin']); })->name('login');
Route::get('/login/{role}', [LoginController::class, 'showLoginForm'])->name('login.portal');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Role-Aware Registration Routes (Defaults to student if no role specified)
Route::get('/register/{role?}', function ($role = 'student') {
    $role = in_array(strtolower($role), ['teacher', 'faculty']) ? 'teacher' : 'student';
    return view('auth.register', compact('role'));
})->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.submit');

/*
|--------------------------------------------------------------------------
| NFC Kiosk & Hardware Polling Endpoints
|--------------------------------------------------------------------------
*/
Route::get('/kiosk', [NfcAttendanceController::class, 'kioskView'])->name('teacher.kiosk');
Route::match(['get', 'post'], '/api/nfc/tap', [NfcAttendanceController::class, 'handleTap'])->name('api.nfc.tap');

// Direct Storage Receiver from Python Bridge (scripts/nfc_bridge.py)
Route::match(['get', 'post'], '/api/nfc/store-tap', function (Request $request) {
    $tagId = strtoupper(trim((string)($request->input('tag_id') ?? $request->query('tag_id'))));
    if (!empty($tagId)) {
        @file_put_contents(storage_path('latest_nfc.txt'), $tagId);
        Cache::put('latest_nfc_tap', $tagId, 300);
        return response()->json(['success' => true, 'tag_id' => $tagId]);
    }
    return response()->json(['success' => false, 'message' => 'Empty tag ID'], 400);
})->name('api.nfc.store-tap');

// Polling Endpoint with Real-Time Duplicate Card Detection
Route::get('/api/nfc/latest-tap', function (Request $request) {
    $tagFile = storage_path('latest_nfc.txt');
    $tagId = file_exists($tagFile) ? trim((string)@file_get_contents($tagFile)) : '';

    $isRegistered = false;
    $ownerName = '';
    $currentUserId = $request->query('current_user_id');

    if (!empty($tagId) && Schema::hasTable('nfc_cards')) {
        $query = DB::table('nfc_cards')->where('tag_id', $tagId);
        if (!empty($currentUserId)) {
            $query->where('user_id', '!=', $currentUserId);
        }
        $card = $query->first();

        if ($card) {
            $isRegistered = true;
            $user = DB::table('users')->where('id', $card->user_id)->first();
            if ($user) {
                $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                $idNum = $user->id_number ?? '';
                $ownerName = $name . ($idNum ? " (ID: {$idNum})" : "");
            } else {
                $ownerName = "Another Registered Student";
            }
        }
    }

    return response()->json([
        'tag_id' => $tagId,
        'is_registered' => $isRegistered,
        'owner_name' => $ownerName
    ])
    ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
    ->header('Pragma', 'no-cache')
    ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
})->name('api.nfc.latest-tap');

/*
|--------------------------------------------------------------------------
| Authenticated User Portals
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Role-Based Landing Redirect
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $roleName = is_object($user->role) ? $user->role->name : $user->role;
        return match ($roleName) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.schedules'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect('/'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

                        // Academic / School Year Dedicated Dashboard Routes
        Route::get('/school-year', function () {
            $totalStudents = \App\Models\User::where('role_id', 3)->count();
            $totalFaculty  = \App\Models\User::where('role_id', 2)->count();
            $totalLogs     = \Illuminate\Support\Facades\Schema::hasTable('attendance_logs') 
                ? \Illuminate\Support\Facades\DB::table('attendance_logs')->count() 
                : 0;
            $recentTaps    = collect();

            $activePeriod = \Illuminate\Support\Facades\Schema::hasTable('academic_periods')
                ? \Illuminate\Support\Facades\DB::table('academic_periods')->where('is_active', 1)->first()
                : null;

            $activeSchoolYear = $activePeriod->school_year ?? \Illuminate\Support\Facades\Cache::get('active_academic_year', '2027-2028');
            $activeSemester   = $activePeriod->semester ?? '2nd Semester';

            return view('admin.school_year', compact('totalStudents', 'totalFaculty', 'totalLogs', 'recentTaps', 'activeSchoolYear', 'activeSemester'));
        })->name('school-year');

        Route::post('/school-year/update', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'admin_password' => 'required',
                'academic_year' => 'required'
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->admin_password, auth()->user()->password)) {
                return back()->with('error', 'Incorrect admin password. Action aborted!');
            }

            $rawYear = (string)$request->input('academic_year', '2027-2028');
            $year = trim(str_replace(["\xe2\x80\x93", "\xe2\x80\x94", '–', '—', ' '], ['-', '-', '-', '-', ''], $rawYear));
            $semester = trim((string)$request->input('semester', '1st Semester'));

            if (\Illuminate\Support\Facades\Schema::hasTable('academic_periods')) {
                $cols = \Illuminate\Support\Facades\Schema::getColumnListing('academic_periods');
                if (in_array('is_active', $cols)) {
                    \Illuminate\Support\Facades\DB::table('academic_periods')->update(['is_active' => 0]);
                }
                $matchCol = in_array('school_year', $cols) ? 'school_year' : (in_array('academic_year', $cols) ? 'academic_year' : 'name');
                $data = [
                    $matchCol    => $year,
                    'is_active'  => 1,
                    'updated_at' => now()
                ];
                if (in_array('semester', $cols)) $data['semester'] = $semester;
                if (in_array('name', $cols)) $data['name'] = 'A.Y. ' . $year;

                $exists = \Illuminate\Support\Facades\DB::table('academic_periods')->where($matchCol, $year)->first();
                if ($exists) {
                    \Illuminate\Support\Facades\DB::table('academic_periods')->where('id', $exists->id)->update($data);
                } else {
                    if (in_array('created_at', $cols)) $data['created_at'] = now();
                    \Illuminate\Support\Facades\DB::table('academic_periods')->insert($data);
                }
            }

            \Illuminate\Support\Facades\Cache::forever('active_academic_year', $year);
            \Illuminate\Support\Facades\Cache::forever('active_semester', $semester);

            return back()->with('success', 'Academic Period successfully updated to ' . $year . ' (' . $semester . ')');
        })->name('school-year.update');

        Route::post('/school-year/reset', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'admin_password' => 'required'
            ]);

            if (!\Illuminate\Support\Facades\Hash::check($request->admin_password, auth()->user()->password)) {
                return back()->with('error', 'Incorrect admin password. Action aborted!');
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('attendance_logs')) {
                \Illuminate\Support\Facades\DB::table('attendance_logs')->truncate();
            }
            return back()->with('success', 'Attendance logs have been safely reset for the new academic year.');
        })->name('school-year.reset');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Schedule Routes
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::post('/schedules/clear-all', [ScheduleController::class, 'clearAll'])->name('schedules.clear-all');
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

        Route::get('/kiosk', [NfcAttendanceController::class, 'kioskView'])->name('kiosk');

        // Non-Admin Export Engine (Strictly Excludes role_id = 1 / admin)
        Route::get('/users/export', function (Request $request) {
            $type = strtolower(trim((string)$request->query('type', 'all')));
            $cols = Schema::getColumnListing('users');
            
            $query = \App\Models\User::with(['role', 'nfcCard'])
                ->where(function ($q) use ($cols) {
                    if (in_array('role_id', $cols)) $q->where('role_id', '!=', 1);
                    if (in_array('role', $cols)) {
                        $q->where('role', '!=', 'admin')->where('role', '!=', 'Admin');
                    }
                })
                ->latest('id');

            if ($type === 'students' || $type === 'student') {
                $query->where(function ($q) use ($cols) {
                    if (in_array('role_id', $cols)) $q->where('role_id', 3);
                    if (in_array('role', $cols)) $q->orWhere('role', 'student')->orWhere('role', 'Student');
                });
                $csvFileName = 'SIATRACK_Students_Directory_' . date('Y-m-d') . '.csv';
                $columns = ['School ID / LRN', 'Student Name', 'Strand', 'Assigned NFC Tag UID', 'Email Address', 'Contact Number', 'Parent Contact', 'Status'];

                $users = $query->get();
                $callback = function() use ($users, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    foreach ($users as $u) {
                        $tagId = $u->nfcCard ? $u->nfcCard->tag_id : 'No Card';
                        fputcsv($file, [
                            $u->id_number ?? 'Not Set',
                            trim($u->first_name . ' ' . $u->last_name),
                            $u->strand ?? 'GENERAL',
                            $tagId,
                            $u->email,
                            $u->phone_number ?? 'N/A',
                            $u->parent_phone_number ?? 'N/A',
                            $u->is_active ? 'Active' : 'Inactive'
                        ]);
                    }
                    fclose($file);
                };
            } elseif ($type === 'faculty' || $type === 'teacher' || $type === 'staff') {
                $query->where(function ($q) use ($cols) {
                    if (in_array('role_id', $cols)) $q->where('role_id', 2);
                    if (in_array('role', $cols)) {
                        $q->orWhere('role', 'teacher')->orWhere('role', 'faculty')->orWhere('role', 'Teacher')->orWhere('role', 'Faculty');
                    }
                });
                $csvFileName = 'SIATRACK_Faculty_Directory_' . date('Y-m-d') . '.csv';
                $columns = ['Employee / Faculty ID', 'Faculty Name', 'Department / Role', 'Email Address', 'Contact Number', 'Status'];

                $users = $query->get();
                $callback = function() use ($users, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    foreach ($users as $u) {
                        fputcsv($file, [
                            $u->id_number ?? 'N/A',
                            trim($u->first_name . ' ' . $u->last_name),
                            'Teacher / Faculty',
                            $u->email,
                            $u->phone_number ?? 'N/A',
                            $u->is_active ? 'Active' : 'Inactive'
                        ]);
                    }
                    fclose($file);
                };
            } else {
                $csvFileName = 'SIATRACK_Users_Directory_Excl_Admin_' . date('Y-m-d') . '.csv';
                $columns = ['Account Type', 'ID / LRN', 'Full Name', 'Email Address', 'Strand / Dept', 'Assigned NFC UID', 'Contact Number', 'Status'];

                $users = $query->get();
                $callback = function() use ($users, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    foreach ($users as $u) {
                        $roleName = ($u->role_id == 2) ? 'Faculty' : 'Student';
                        $tagId = $u->nfcCard ? $u->nfcCard->tag_id : 'N/A';
                        fputcsv($file, [
                            $roleName,
                            $u->id_number ?? 'N/A',
                            trim($u->first_name . ' ' . $u->last_name),
                            $u->email,
                            $u->strand ?? 'General',
                            $tagId,
                            $u->phone_number ?? 'N/A',
                            $u->is_active ? 'Active' : 'Inactive'
                        ]);
                    }
                    fclose($file);
                };
            }

            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$csvFileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];
            return response()->stream($callback, 200, $headers);
        })->name('users.export');

        Route::post('/profile/update', [AdminUserController::class, 'updateProfile'])->name('profile.update');
        Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', AdminUserController::class);
        
        Route::get('/attendance', function (Request $request) {
            $today = Carbon::today()->toDateString();
            $totalStudents = \App\Models\User::where('role_id', 3)->count();
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $presentTodayCount = 0; $lateTodayCount = 0; $absentTodayCount = $totalStudents;
            $smsLogs = collect();

            if (Schema::hasTable('attendance_logs')) {
                $cols = Schema::getColumnListing('attendance_logs');
                $foreignKey = in_array('student_id', $cols) ? 'student_id' : (in_array('user_id', $cols) ? 'user_id' : null);
                $dateCol = in_array('attendance_date', $cols) ? 'attendance_date' : (in_array('date', $cols) ? 'date' : 'created_at');

                $todayQuery = DB::table('attendance_logs')->whereDate($dateCol, $today);
                if ($foreignKey) { $presentTodayCount = (clone $todayQuery)->distinct($foreignKey)->count($foreignKey); }
                else { $presentTodayCount = (clone $todayQuery)->count(); }

                if (in_array('status', $cols)) { $lateTodayCount = (clone $todayQuery)->where('status', 'LATE')->count(); }
                $absentTodayCount = max(0, $totalStudents - $presentTodayCount);

                $query = DB::table('attendance_logs');
                if ($foreignKey) { $query->leftJoin('users', 'attendance_logs.' . $foreignKey, '=', 'users.id'); }
                $logs = $query->select('attendance_logs.*', 'users.first_name', 'users.last_name', 'users.id_number', DB::raw("NULL as strand"))->latest('attendance_logs.created_at')->paginate(20);
            }

            $attendanceRate = $totalStudents > 0 ? round(($presentTodayCount / $totalStudents) * 100, 1) . '%' : '0%';
            $lateRate = $totalStudents > 0 ? round(($lateTodayCount / $totalStudents) * 100, 1) . '%' : '0%';
            $absentRate = $totalStudents > 0 ? round(($absentTodayCount / $totalStudents) * 100, 1) . '%' : '0%';

            return view('admin.attendance', compact('logs', 'totalStudents', 'presentTodayCount', 'lateTodayCount', 'absentTodayCount', 'attendanceRate', 'lateRate', 'absentRate', 'smsLogs'));
        })->name('attendance');

        Route::get('/attendance/export', function () {
            $cols = Schema::hasTable('attendance_logs') ? Schema::getColumnListing('attendance_logs') : [];
            $foreignKey = in_array('student_id', $cols) ? 'student_id' : (in_array('user_id', $cols) ? 'user_id' : 'student_id');
            $logs = collect();
            if (Schema::hasTable('attendance_logs')) {
                $logs = DB::table('attendance_logs')->leftJoin('users', 'attendance_logs.' . $foreignKey, '=', 'users.id')->select('attendance_logs.*', 'users.first_name', 'users.last_name', 'users.id_number', DB::raw("NULL as strand"))->latest('attendance_logs.created_at')->get();
            }
            $csvFileName = 'SIATRACK_Attendance_' . date('Y-m-d') . '.csv';
            $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$csvFileName", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0"];
            $columns = ['Student Name', 'School ID', 'Strand', 'Date', 'Time In', 'Status'];

            $callback = function() use($logs, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($logs as $l) {
                    fputcsv($file, [($l->first_name ?? '') . ' ' . ($l->last_name ?? ''), $l->id_number ?? 'N/A', $l->strand ?? 'General', $l->attendance_date ?? $l->created_at ?? date('Y-m-d'), $l->time_in ?? 'N/A', $l->status ?? 'ON-TIME']);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        })->name('attendance.export');

        Route::get('/evaluations', [AdminDashboardController::class, 'evaluations'])->name('evaluations');
        Route::get('/reports', function () {
            $totalStudents = \App\Models\User::where('role_id', 3)->count();
            $totalFaculty  = \App\Models\User::where('role_id', 2)->count();
            $totalLogs     = Schema::hasTable('attendance_logs') ? DB::table('attendance_logs')->count() : 0;
            $totalEvals    = Schema::hasTable('evaluation_submissions') ? DB::table('evaluation_submissions')->count() : 0;
            return view('admin.reports', compact('totalStudents', 'totalFaculty', 'totalLogs', 'totalEvals'));
        })->name('reports');
    });

/*
    |--------------------------------------------------------------------------
    | Teacher / Faculty Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/schedule', [TeacherDashboardController::class, 'schedule'])->name('schedules');
        Route::post('/schedule/update', [TeacherDashboardController::class, 'updateSchedule'])->name('schedule.update');
        Route::get('/schedule/{id}/students', [TeacherDashboardController::class, 'classList'])->name('schedule.students');
        Route::put('/profile/update', [TeacherDashboardController::class, 'updateProfile'])->name('profile.update');
        
        // --- UPDATED ATTENDANCE & EXPORT ROUTES ---
        Route::get('/attendance', [NfcAttendanceController::class, 'attendanceIndex'])->name('attendance');
        Route::get('/attendance/export', [NfcAttendanceController::class, 'exportCsv'])->name('attendance.export');
        
        Route::get('/evaluation-report', [TeacherDashboardController::class, 'evaluationReport'])->name('evaluation.report');
        Route::get('/kiosk', [NfcAttendanceController::class, 'kioskView'])->name('kiosk');
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
