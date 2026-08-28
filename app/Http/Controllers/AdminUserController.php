<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\NfcCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = strtolower(trim((string)$request->query('role', 'all')));
        $search = trim((string)$request->query('search', ''));
        $cols = Schema::getColumnListing('users');

        $baseQuery = User::with(['role', 'nfcCard'])->latest('id');

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search, $cols) {
                if (in_array('first_name', $cols)) $q->where('first_name', 'like', "%{$search}%");
                if (in_array('last_name', $cols)) $q->orWhere('last_name', 'like', "%{$search}%");
                if (in_array('name', $cols)) $q->orWhere('name', 'like', "%{$search}%");
                if (in_array('email', $cols)) $q->orWhere('email', 'like', "%{$search}%");
                if (in_array('id_number', $cols)) $q->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        // Students Query (Role ID 3)
        $studentsQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 3);
            if (in_array('role', $cols)) $q->orWhere('role', 'student')->orWhere('role', 'Student');
        });

        // Faculty Query (Role ID 2)
        $facultyQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 2);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'teacher')->orWhere('role', 'faculty')->orWhere('role', 'Teacher')->orWhere('role', 'Faculty');
            }
        });

        // Admins Query (Role ID 1)
        $adminsQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 1);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'admin')->orWhere('role', 'Admin');
            }
        });

        $students = $studentsQuery->paginate(15, ['*'], 'students_page');
        $faculty = $facultyQuery->paginate(15, ['*'], 'faculty_page');
        $admins = $adminsQuery->paginate(15, ['*'], 'admins_page');

        $totalUsers = User::count();
        $studentCount = User::where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 3);
            if (in_array('role', $cols)) $q->orWhere('role', 'student')->orWhere('role', 'Student');
        })->count();

        $teacherCount = User::where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 2);
            if (in_array('role', $cols)) $q->orWhere('role', 'teacher')->orWhere('role', 'faculty')->orWhere('role', 'Teacher')->orWhere('role', 'Faculty');
        })->count();

        $adminCount = User::where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 1);
            if (in_array('role', $cols)) $q->orWhere('role', 'admin')->orWhere('role', 'Admin');
        })->count();

        $roles = Schema::hasTable('roles') ? Role::all() : collect();

        return view('admin.users.index', compact('students', 'faculty', 'admins', 'roles', 'totalUsers', 'studentCount', 'teacherCount', 'adminCount', 'search', 'roleFilter'));
    }

    public function create()
    {
        @file_put_contents(storage_path('latest_nfc.txt'), '');
        Cache::forget('latest_nfc_tap');

        $roles = Schema::hasTable('roles') ? Role::all() : collect();
        if ($roles->isEmpty()) {
            $roles = collect([
                (object)['id' => 3, 'name' => 'student'],
                (object)['id' => 2, 'name' => 'teacher'],
                (object)['id' => 1, 'name' => 'admin'],
            ]);
        }
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $roleId = (int)$request->input('role_id', 3);
        $isStudent = ($roleId === 3);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $request->validate($rules);

        if ($isStudent && $request->filled('nfc_tag_id') && Schema::hasTable('nfc_cards')) {
            $cleanTag = strtoupper(trim($request->nfc_tag_id));
            $existingCard = NfcCard::with('user')->where('tag_id', $cleanTag)->first();
            if ($existingCard) {
                $owner = $existingCard->user ? "{$existingCard->user->first_name} {$existingCard->user->last_name}" : "another user";
                return back()->withInput()->withErrors(['nfc_tag_id' => "NFC Card [{$cleanTag}] is already registered to {$owner}."]);
            }
        }

        try {
            $cols = Schema::getColumnListing('users');
            $roleString = match ($roleId) {
                1 => 'admin',
                2 => 'teacher',
                default => 'student',
            };

            $userData = [];
            if (in_array('first_name', $cols)) $userData['first_name'] = $request->first_name;
            if (in_array('last_name', $cols)) $userData['last_name'] = $request->last_name;
            if (in_array('name', $cols)) $userData['name'] = trim($request->first_name . ' ' . $request->last_name);
            if (in_array('email', $cols)) $userData['email'] = $request->email;
            
            $plainPassword = $request->password;
            $userData['password'] = Hash::make($plainPassword);
            
            if (in_array('role_id', $cols)) $userData['role_id'] = $roleId;
            if (in_array('role', $cols)) $userData['role'] = $roleString;

            if (in_array('id_number', $cols)) $userData['id_number'] = $request->id_number;
            if (in_array('gender', $cols)) $userData['gender'] = $request->gender;
            if (in_array('phone_number', $cols)) $userData['phone_number'] = $request->phone_number;
            if (in_array('grade_level', $cols)) $userData['grade_level'] = $isStudent ? $request->grade_level : null;
            
            $selectedTrack = $request->input('track', $request->input('strand'));
            if (in_array('strand', $cols)) $userData['strand'] = $isStudent ? $selectedTrack : null;
            if (in_array('track', $cols)) $userData['track'] = $isStudent ? $selectedTrack : null;
            
            if (in_array('section', $cols)) $userData['section'] = $isStudent ? $request->section : null;
            if (in_array('parent_name', $cols)) $userData['parent_name'] = $isStudent ? $request->parent_name : null;
            if (in_array('parent_phone_number', $cols)) $userData['parent_phone_number'] = $isStudent ? $request->parent_phone_number : null;
            
            if (in_array('is_active', $cols)) $userData['is_active'] = 1;
            if (in_array('status', $cols)) $userData['status'] = 'active';

            $user = User::create($userData);

            if ($isStudent && $request->filled('nfc_tag_id') && Schema::hasTable('nfc_cards')) {
                NfcCard::updateOrCreate(
                    ['user_id' => $user->id],
                    ['tag_id' => strtoupper(trim($request->nfc_tag_id))]
                );
            }

            @file_put_contents(storage_path('latest_nfc.txt'), '');
            Cache::forget('latest_nfc_tap');

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$request->first_name} {$request->last_name}' successfully added!")
                ->with('new_user_created', [
                    'name'      => "{$user->first_name} {$user->last_name}",
                    'role'      => ucfirst($roleString),
                    'id_number' => $user->id_number ?? 'N/A',
                    'email'     => $user->email,
                    'password'  => $plainPassword,
                    'nfc_tag'   => $request->filled('nfc_tag_id') ? strtoupper(trim($request->nfc_tag_id)) : null,
                ]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = User::with('nfcCard')->findOrFail($id);
        $roles = Schema::hasTable('roles') ? Role::all() : collect();
        if ($roles->isEmpty()) {
            $roles = collect([
                (object)['id' => 3, 'name' => 'student'],
                (object)['id' => 2, 'name' => 'teacher'],
                (object)['id' => 1, 'name' => 'admin'],
            ]);
        }
        return view('admin.users.create', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roleId = (int)$request->input('role_id', $user->role_id ?? 3);
        $isStudent = ($roleId === 3);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        $hasNewPassword = $request->filled('password') && $request->password !== '••••••••';
        if ($hasNewPassword) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        if ($isStudent && $request->filled('nfc_tag_id') && Schema::hasTable('nfc_cards')) {
            $cleanTag = strtoupper(trim($request->nfc_tag_id));
            $existingCard = NfcCard::with('user')->where('tag_id', $cleanTag)->where('user_id', '!=', $id)->first();
            if ($existingCard) {
                $owner = $existingCard->user ? "{$existingCard->user->first_name} {$existingCard->user->last_name}" : "another user";
                return back()->withInput()->withErrors(['nfc_tag_id' => "NFC Card [{$cleanTag}] is already registered to {$owner}."]);
            }
        }

        try {
            $cols = Schema::getColumnListing('users');
            $roleString = match ($roleId) {
                1 => 'admin',
                2 => 'teacher',
                default => 'student',
            };

            if (in_array('first_name', $cols)) $user->first_name = $request->first_name;
            if (in_array('last_name', $cols)) $user->last_name = $request->last_name;
            if (in_array('name', $cols)) $user->name = trim($request->first_name . ' ' . $request->last_name);
            if (in_array('email', $cols)) $user->email = $request->email;
            if (in_array('role_id', $cols)) $user->role_id = $roleId;
            if (in_array('role', $cols)) $user->role = $roleString;
            if (in_array('id_number', $cols)) $user->id_number = $request->id_number;
            if (in_array('gender', $cols)) $user->gender = $request->gender;
            if (in_array('phone_number', $cols)) $user->phone_number = $request->phone_number;
            if (in_array('grade_level', $cols)) $user->grade_level = $isStudent ? $request->grade_level : null;
            
            $selectedTrack = $request->input('track', $request->input('strand'));
            if (in_array('strand', $cols)) $user->strand = $isStudent ? $selectedTrack : null;
            if (in_array('track', $cols)) $user->track = $isStudent ? $selectedTrack : null;

            if (in_array('section', $cols)) $user->section = $isStudent ? $request->section : null;
            if (in_array('parent_name', $cols)) $user->parent_name = $isStudent ? $request->parent_name : null;
            if (in_array('parent_phone_number', $cols)) $user->parent_phone_number = $isStudent ? $request->parent_phone_number : null;

            if ($hasNewPassword) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if (Schema::hasTable('nfc_cards')) {
                if (!$isStudent || !$request->filled('nfc_tag_id')) {
                    NfcCard::where('user_id', $user->id)->delete();
                } else {
                    NfcCard::updateOrCreate(
                        ['user_id' => $user->id],
                        ['tag_id' => strtoupper(trim($request->nfc_tag_id))]
                    );
                }
            }

            @file_put_contents(storage_path('latest_nfc.txt'), '');
            Cache::forget('latest_nfc_tap');

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$user->first_name} {$user->last_name}' updated successfully!");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Update Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $name = "{$user->first_name} {$user->last_name}";
        if (Schema::hasTable('nfc_cards')) {
            NfcCard::where('user_id', $user->id)->delete();
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User '{$name}' deleted successfully!");
    }
}