<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm($role = 'admin')
    {
        $allowedRoles = ['admin', 'teacher', 'student'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'admin';
        }

        return view('auth.login', compact('role'));
    }

    public function login(Request $request)
    {
        $expectedRole = strtolower($request->input('expected_role', 'admin'));
        $isAdmin = ($expectedRole === 'admin');

        // 1. Validation Rules: Admin only needs email & password, others need id_number, email, & password
        $rules = [
            'email'         => ['required', 'email'],
            'password'      => ['required', 'string'],
            'expected_role' => ['required', 'string', 'in:admin,teacher,student'],
        ];

        if (!$isAdmin) {
            $rules['id_number'] = ['required', 'string'];
        }

        $request->validate($rules);

        // 2. Build credentials array based on portal type
        $authCredentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (!$isAdmin) {
            $authCredentials['id_number'] = $request->id_number;
        }

        if (Auth::attempt($authCredentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Determine actual role
            $actualRole = 'student';
            if ($user->role_id == 1 || (is_object($user->role) && strtolower($user->role->name) === 'admin')) {
                $actualRole = 'admin';
            } elseif ($user->role_id == 2 || (is_object($user->role) && in_array(strtolower($user->role->name), ['teacher', 'faculty']))) {
                $actualRole = 'teacher';
            } elseif ($user->role_id == 3 || (is_object($user->role) && strtolower($user->role->name) === 'student')) {
                $actualRole = 'student';
            }

            // Strict Role Matching
            if ($actualRole !== $expectedRole) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $portalName = match($expectedRole) {
                    'admin'   => 'Admin Access Portal',
                    'teacher' => 'Faculty Login Portal',
                    'student' => 'Student Access / Evaluation Portal',
                    default   => 'Portal',
                };

                $userRoleTitle = match($actualRole) {
                    'admin'   => 'an Administrator',
                    'teacher' => 'a Faculty member',
                    'student' => 'a Student',
                    default   => 'another role',
                };

                return back()
                    ->withInput($request->only('id_number', 'email', 'expected_role'))
                    ->withErrors([
                        'email' => "Access Denied: Your account is {$userRoleTitle}. You are not permitted to log in through the {$portalName}."
                    ]);
            }

            $request->session()->regenerate();

            return match ($actualRole) {
                'admin'   => redirect()->route('admin.dashboard'),
                'teacher' => redirect()->route('teacher.schedules'),
                'student' => redirect()->route('student.dashboard'),
                default   => redirect('/'),
            };
        }

        return back()
            ->withInput($request->only('id_number', 'email', 'expected_role'))
            ->withErrors([
                'email' => $isAdmin 
                    ? 'Invalid credentials. Please verify your Gmail and password.' 
                    : 'Invalid credentials. Please verify your ID Number, Gmail, and password.'
            ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'id_number'  => ['required', 'string', 'max:50', 'unique:users,id_number'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'role_id'    => ['required', 'in:2,3'],
        ], [
            'id_number.unique'   => 'This School / Faculty ID is already registered.',
            'email.unique'       => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min'       => 'Password must be at least 6 characters.',
        ]);

        $roleId = (int) $validated['role_id'];
        $roleName = ($roleId === 2) ? 'teacher' : 'student';

        $userData = [
            'first_name' => trim($validated['first_name']),
            'last_name'  => trim($validated['last_name']),
            'id_number'  => trim($validated['id_number']),
            'email'      => strtolower(trim($validated['email'])),
            'password'   => Hash::make($validated['password']),
            'role_id'    => $roleId,
            'is_active'  => true,
        ];

        if (Schema::hasColumn('users', 'role')) {
            $userData['role'] = $roleName;
        }

        if (Schema::hasColumn('users', 'name')) {
            $userData['name'] = trim($validated['first_name'] . ' ' . $validated['last_name']);
        }

        User::create($userData);

        Auth::logout();

        $roleSlug = ($roleId === 2) ? 'teacher' : 'student';

        return redirect()->route('login.portal', ['role' => $roleSlug])
            ->with('success', 'Account created successfully! Please sign in using your credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}