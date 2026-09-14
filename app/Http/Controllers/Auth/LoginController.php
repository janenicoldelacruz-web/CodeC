<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Unified login handler: Email and plain-text password for all roles.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 1. Hanapin ang user gamit ang lowercase email para case-insensitive
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

        // 2. Direct string comparison (Plain text check - iwas BcryptHasher crash)
        if ($user && $user->password === $request->password) {

            // Check kung active ang account
            if (isset($user->is_active) && ! $user->is_active) {
                return back()->withErrors([
                    'email' => 'Your account is deactivated. Please contact the administrator.'
                ])->onlyInput('email');
            }

            // 3. Manu-manong i-authenticate ang session
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            // Tukuyin ang role base sa role_id (1 = Admin, 2 = Faculty, 3 = Student, 4 = Director / Super Admin Viewer)
            $actualRole = 'student';

            if (
                $user->role_id == 1 || 
                $user->role_id == 4 || 
                (is_object($user->role) && in_array(strtolower($user->role->name), ['admin', 'superadmin_viewer', 'director'])) || 
                (isset($user->role) && in_array(strtolower($user->role), ['admin', 'superadmin_viewer', 'director']))
            ) {
                $actualRole = 'admin';
            } elseif (
                $user->role_id == 2 || 
                (is_object($user->role) && in_array(strtolower($user->role->name), ['teacher', 'faculty'])) || 
                (isset($user->role) && in_array(strtolower($user->role), ['teacher', 'faculty']))
            ) {
                $actualRole = 'teacher';
            } elseif (
                $user->role_id == 3 || 
                (is_object($user->role) && strtolower($user->role->name) === 'student') || 
                (isset($user->role) && strtolower($user->role) === 'student')
            ) {
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