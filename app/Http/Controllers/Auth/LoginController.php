<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm($role)
    {
        $validRoles = ['admin', 'teacher', 'student'];
        
        if (!in_array($role, $validRoles)) {
            abort(404);
        }

        return view('auth.login', compact('role'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is deactivated.']);
            }

            $roleName = is_object($user->role) ? $user->role->name : $user->role;

            // Explicitly route straight to the admin portal
            if ($roleName === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($roleName === 'teacher') {
                return redirect()->route('teacher.dashboard');
            } elseif ($roleName === 'student') {
                return redirect()->route('student.dashboard');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}