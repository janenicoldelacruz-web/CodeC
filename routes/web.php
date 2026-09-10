<?php

// Non-Admin Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\NfcAttendanceController;

// Admin Controllers (Inside Admin Folder Subspace)
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSchoolYearController;
use App\Http\Controllers\Admin\AdminNfcController;
use App\Http\Controllers\Admin\AdminSectionController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminEvaluationController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminSettingController;
/*
|--------------------------------------------------------------------------
| Public & Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('welcome');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

/*
|--------------------------------------------------------------------------
| NFC Kiosk & Hardware Polling Endpoints
|--------------------------------------------------------------------------
*/
Route::get('/kiosk', [NfcAttendanceController::class, 'kioskView'])->name('teacher.kiosk');
Route::match(['get', 'post'], '/api/nfc/tap', [NfcAttendanceController::class, 'handleTap'])->name('api.nfc.tap');
Route::match(['get', 'post'], '/api/nfc/store-tap', [NfcAttendanceController::class, 'storeTap'])->name('api.nfc.store-tap');
Route::get('/api/nfc/latest-tap', [NfcAttendanceController::class, 'latestTap'])->name('api.nfc.latest-tap');

/*
|--------------------------------------------------------------------------
| Authenticated User Portals
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Role-Based Landing Redirect
    Route::get('/dashboard', [LoginController::class, 'redirectDashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::post('/profile/update', [AdminUserController::class, 'updateProfile'])->name('profile.update');
        Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::resource('users', AdminUserController::class);

        // NFC Management
        Route::get('/nfc/binding', [AdminNfcController::class, 'binding'])->name('nfc.binding');
        Route::get('/nfc/replacement', [AdminNfcController::class, 'replacement'])->name('nfc.replacement');

        // Academic Setup
        Route::get('/school-year', [AdminSchoolYearController::class, 'index'])->name('school-year');
        Route::post('/school-year/update', [AdminSchoolYearController::class, 'update'])->name('school-year.update');
        Route::post('/school-year/reset', [AdminSchoolYearController::class, 'reset'])->name('school-year.reset');
        
        Route::get('/sections', [AdminSectionController::class, 'index'])->name('sections');
        
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::post('/schedules/clear-all', [ScheduleController::class, 'clearAll'])->name('schedules.clear-all');
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

        // Attendance Monitoring
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance');
        Route::get('/attendance/live', [AdminAttendanceController::class, 'live'])->name('attendance.live');
        Route::get('/attendance/override', [AdminAttendanceController::class, 'override'])->name('attendance.override');
        Route::get('/attendance/export', [AdminAttendanceController::class, 'export'])->name('attendance.export');

        // Faculty Evaluation
        Route::get('/evaluations', [AdminEvaluationController::class, 'index'])->name('evaluations');
        Route::get('/evaluations/periods', [AdminEvaluationController::class, 'periods'])->name('evaluations.periods');
        Route::get('/evaluations/results', [AdminEvaluationController::class, 'results'])->name('evaluations.results');

        // Announcements
        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements');

        // Reports
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::get('/reports/attendance', [AdminReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/sf2', [AdminReportController::class, 'sf2'])->name('reports.sf2');
        Route::get('/reports/evaluation', [AdminReportController::class, 'evaluation'])->name('reports.evaluation');
        Route::get('/reports/users', [AdminReportController::class, 'users'])->name('reports.users');

        // Audit Logs & Settings
        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings');

        Route::get('/kiosk', [NfcAttendanceController::class, 'kioskView'])->name('kiosk');
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

});

// Global Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');