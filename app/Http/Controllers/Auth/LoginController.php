<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        // 100% Plain Text Comparison - Walang Bcrypt
        if ($user && $user->password === $request->password) {
            if (isset($user->is_active) && !$user->is_active) {
                return back()->withErrors([
                    'email' => 'Your account is deactivated. Please contact the administrator.'
                ])->onlyInput('email');
            }

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            $actualRole = 'student';
            
            if (
                $user->role_id == 1 || 
                $user->role_id == 4 || 
                (is_object($user->role) && in_array(strtolower($user->role->name), ['admin', 'superadmin_viewer'])) || 
                (isset($user->role) && in_array($user->role, ['admin', 'superadmin_viewer']))
            ) {
                $actualRole = 'admin';
            } elseif (
                $user->role_id == 2 || 
                (is_object($user->role) && in_array(strtolower($user->role->name), ['teacher', 'faculty'])) || 
                (isset($user->role) && in_array($user->role, ['teacher', 'faculty']))
            ) {
                $actualRole = 'teacher';
            } elseif (
                $user->role_id == 3 || 
                (is_object($user->role) && strtolower($user->role->name) === 'student') || 
                (isset($user->role) && $user->role === 'student')
            ) {
                $actualRole = 'student';
            }

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}