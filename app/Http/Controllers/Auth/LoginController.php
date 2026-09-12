<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the unified login / welcome page.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Unified login handler: Email and password only for all roles.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Check kung active ang account
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account is deactivated. Please contact the administrator.'
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Tukuyin ang role base sa role_id (1 = Admin, 2 = Faculty/Teacher, 3 = Student, 4 = Super Admin Viewer)
            $actualRole = 'student';
            
            // DITO NATIN IDINAGDAG YUNG ROLE ID 4 AT SUPERADMIN_VIEWER
            if ($user->role_id == 1 || $user->role_id == 4 || (is_object($user->role) && in_array(strtolower($user->role->name), ['admin', 'superadmin_viewer'])) || (isset($user->role) && in_array($user->role, ['admin', 'superadmin_viewer']))) {
                $actualRole = 'admin';
            } elseif ($user->role_id == 2 || (is_object($user->role) && in_array(strtolower($user->role->name), ['teacher', 'faculty'])) || (isset($user->role) && in_array($user->role, ['teacher', 'faculty']))) {
                $actualRole = 'teacher';
            } elseif ($user->role_id == 3 || (is_object($user->role) && strtolower($user->role->name) === 'student') || (isset($user->role) && $user->role === 'student')) {
                $actualRole = 'student';
            }

            // Redirect sa kani-kanilang dashboard
            return match ($actualRole) {
                'admin'   => redirect()->route('admin.dashboard'),
                'teacher' => redirect()->route('teacher.schedules'),
                'student' => redirect()->route('student.dashboard'),
                default   => redirect('/'),
            };
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Invalid credentials. Please verify your email and password.'
            ]);
    }

    /**
     * Logout handler.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}