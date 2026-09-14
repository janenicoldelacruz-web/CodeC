<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\NfcCard;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        $studentsQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 3);
            if (in_array('role', $cols)) $q->orWhere('role', 'student')->orWhere('role', 'Student');
        });

        $facultyQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 2);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'teacher')->orWhere('role', 'faculty')->orWhere('role', 'Teacher')->orWhere('role', 'Faculty');
            }
        });

        $directorsQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 4);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'director')->orWhere('role', 'Director');
            }
        });

        $adminsQuery = (clone $baseQuery)->where(function ($q) use ($cols) {
            if (in_array('role_id', $cols)) $q->where('role_id', 1);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'admin')->orWhere('role', 'Admin');
            }
        });

        $students = $studentsQuery->paginate(15, ['*'], 'students_page');
        $faculty = $facultyQuery->paginate(15, ['*'], 'faculty_page');
        $directors = $directorsQuery->paginate(15, ['*'], 'directors_page');
        $admins = $adminsQuery->paginate(15, ['*'], 'admins_page');

        $totalUsers = User::count();
        $studentCount = (clone $studentsQuery)->count();
        $teacherCount = (clone $facultyQuery)->count();
        $directorCount = (clone $directorsQuery)->count();
        $adminCount = (clone $adminsQuery)->count();

        return view('admin.users.index', compact(
            'students', 'faculty', 'directors', 'admins', 
            'totalUsers', 'studentCount', 'teacherCount', 'directorCount', 'adminCount', 
            'search', 'roleFilter'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'all');
        $filename = "siatrack_users_{$type}_" . date('Y-m-d') . ".xlsx";

        return Excel::download(new UsersExport($type), $filename);
    }

    public function create()
    {
        $roles = Schema::hasTable('roles') ? Role::all() : collect([
            (object)['id' => 3, 'name' => 'student'],
            (object)['id' => 2, 'name' => 'teacher'],
            (object)['id' => 4, 'name' => 'director'],
        ]);

        $sections = Schema::hasTable('academic_sections') ? DB::table('academic_sections')->get() : collect();

        $gradeLevels = $sections->pluck('grade_level')
            ->map(fn($v) => ucwords(strtolower(trim($v))))
            ->unique()
            ->filter()
            ->values();

        return view('admin.users.create', compact('roles', 'sections', 'gradeLevels'));
    }

    public function store(Request $request)
    {
        $roleId = (int)$request->input('role_id', 3);
        $isStudent = ($roleId === 3);

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $cols = Schema::getColumnListing('users');
            $roleString = match ($roleId) {
                1 => 'admin',
                2 => 'teacher',
                4 => 'director',
                default => 'student',
            };

            $userData = [];
            if (in_array('first_name', $cols)) $userData['first_name'] = $request->first_name;
            if (in_array('last_name', $cols)) $userData['last_name'] = $request->last_name;
            if (in_array('name', $cols)) $userData['name'] = trim($request->first_name . ' ' . $request->last_name);
            if (in_array('email', $cols)) $userData['email'] = $request->email;
            
            // Plain-text password storage
            $userData['password'] = $request->password;
            
            if (in_array('role_id', $cols)) $userData['role_id'] = $roleId;
            if (in_array('role', $cols)) $userData['role'] = $roleString;

            if (in_array('id_number', $cols)) $userData['id_number'] = $request->id_number;
            if (in_array('gender', $cols)) $userData['gender'] = $request->gender;
            if (in_array('phone_number', $cols)) $userData['phone_number'] = $request->phone_number;

            if (in_array('grade_level', $cols)) {
                $userData['grade_level'] = $isStudent ? $request->grade_level : null;
            }
            if (in_array('strand', $cols)) {
                $userData['strand'] = $isStudent ? $request->strand : null;
            }
            if (in_array('track', $cols)) {
                $userData['track'] = $isStudent ? ($request->strand ?? $request->track) : null;
            }
            if (in_array('section', $cols)) {
                $userData['section'] = $isStudent ? $request->section : null;
            }
            if (in_array('parent_name', $cols)) {
                $userData['parent_name'] = $isStudent ? $request->parent_name : null;
            }
            if (in_array('parent_phone_number', $cols)) {
                $userData['parent_phone_number'] = $isStudent ? $request->parent_phone_number : null;
            }

            $user = User::create($userData);

            if ($isStudent && $request->filled('nfc_tag_id') && Schema::hasTable('nfc_cards')) {
                NfcCard::updateOrCreate(
                    ['user_id' => $user->id],
                    ['tag_id' => strtoupper(trim($request->nfc_tag_id))]
                );
            }

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$request->first_name} {$request->last_name}' successfully added!");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = User::with('nfcCard')->findOrFail($id);
        
        $roles = Schema::hasTable('roles') ? Role::all() : collect([
            (object)['id' => 3, 'name' => 'student'],
            (object)['id' => 2, 'name' => 'teacher'],
            (object)['id' => 4, 'name' => 'director'],
        ]);

        $sections = Schema::hasTable('academic_sections') ? DB::table('academic_sections')->get() : collect();

        $gradeLevels = $sections->pluck('grade_level')
            ->map(fn($v) => ucwords(strtolower(trim($v))))
            ->unique()
            ->filter()
            ->values();

        return view('admin.users.edit', compact('user', 'roles', 'sections', 'gradeLevels'));
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

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        try {
            $cols = Schema::getColumnListing('users');
            $roleString = match ($roleId) {
                1 => 'admin',
                2 => 'teacher',
                4 => 'director',
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
            
            if (in_array('grade_level', $cols)) {
                $user->grade_level = $isStudent ? $request->grade_level : null;
            }
            if (in_array('strand', $cols)) {
                $user->strand = $isStudent ? $request->strand : null;
            }
            if (in_array('track', $cols)) {
                $user->track = $isStudent ? ($request->strand ?? $request->track) : null;
            }
            if (in_array('section', $cols)) {
                $user->section = $isStudent ? $request->section : null;
            }
            if (in_array('parent_name', $cols)) {
                $user->parent_name = $isStudent ? $request->parent_name : null;
            }
            if (in_array('parent_phone_number', $cols)) {
                $user->parent_phone_number = $isStudent ? $request->parent_phone_number : null;
            }

            // Plain-text password update
            if ($request->filled('password')) {
                $user->password = $request->password;
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

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$user->first_name} {$user->last_name}' updated successfully!");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Update Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the logged-in administrator's profile from the dashboard modal.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name'            => ['required', 'string', 'max:255'],
            'last_name'             => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number'          => ['nullable', 'string', 'max:20'],
            'current_password'      => ['required', 'string'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Plain text validation laban sa current password
        if ($user->password !== $request->current_password) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.'
            ]);
        }

        $cols = Schema::getColumnListing('users');

        if (in_array('first_name', $cols)) $user->first_name = $request->first_name;
        if (in_array('last_name', $cols)) $user->last_name = $request->last_name;
        if (in_array('name', $cols)) $user->name = trim($request->first_name . ' ' . $request->last_name);
        if (in_array('email', $cols)) $user->email = $request->email;

        if (in_array('phone_number', $cols)) {
            $user->phone_number = $request->phone_number;
        } elseif (in_array('contact_number', $cols)) {
            $user->contact_number = $request->phone_number;
        }

        // Direct plain-text password save kung may bagong password
        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();

return back()->with('profile_success', 'Administrator profile updated successfully!');
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