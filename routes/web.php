<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return redirect()->route('login.portal', ['role' => 'admin']);
})->name('login');

Route::get('/login/{role}', [LoginController::class, 'showLoginForm'])->name('login.portal');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Smart fallback route for /dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $roleName = is_object($user->role) ? $user->role->name : $user->role;

        return match ($roleName) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect('/'),
        };
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  // 1. Admin Portal Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            // Kinukuha ang quick stats para sa dashboard
            $totalStudents = \App\Models\User::where('role_id', 3)->count();
            $totalFaculty  = \App\Models\User::where('role_id', 2)->count();
            $activeSMS     = 112; // Base metric sa documentation
            $attendanceRate = '94.7%';
            return view('admin.dashboard', compact('totalStudents', 'totalFaculty', 'activeSMS', 'attendanceRate'));
        })->name('dashboard');
        
        // Admin Profile Update Route
        Route::post('/profile/update', [AdminUserController::class, 'updateProfile'])->name('profile.update');
        
        Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', AdminUserController::class);
        
        Route::get('/attendance', function () {
            return view('admin.attendance');
        })->name('attendance');
        
        Route::get('/evaluations', function () {
            return view('admin.evaluations');
        })->name('evaluations');
        
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');
    });

   // 2. Teacher / Faculty Portal Routes
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::post('/schedule/update', [TeacherDashboardController::class, 'updateSchedule'])->name('schedule.update');
        Route::post('/profile/update', [TeacherDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/absence-reporting', [TeacherDashboardController::class, 'absenceReporting'])->name('absence.reporting');
        Route::get('/evaluation-report', [TeacherDashboardController::class, 'evaluationReport'])->name('evaluation.report');
    });

    // 3. Student Portal Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', function () {
            return view('student.dashboard');
        })->name('dashboard');
    });

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

});