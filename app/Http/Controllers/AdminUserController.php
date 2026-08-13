<?php

namespace App\Http\Controllers;

use App\Models\AcademicSection;
use App\Models\NfcCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display users.
     */
    public function index(Request $request)
    {
        $query = User::with([
            'role',
            'nfcCard',
            'sections'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('id_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Role Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('role')) {

            $query->whereHas('role', function ($q) use ($request) {

                $q->whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($request->role)]
                );

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Grade / Section Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('grade_section')) {

            $query->whereHas('sections', function ($q) use ($request) {

                $q->where('id', $request->grade_section);

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $users = $query
            ->orderBy('id', 'asc')      
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $activeUsers = User::where('is_active', true)->count();

        $inactiveUsers = User::where('is_active', false)->count();

        $totalUsers = User::count();

        $studentCount = User::whereHas('role', function ($q) {

            $q->whereRaw(
                'LOWER(name) = ?',
                ['student']
            );

        })->count();

        $teacherCount = User::whereHas('role', function ($q) {

            $q->whereRaw(
                'LOWER(name) = ?',
                ['teacher']
            );

        })->count();

        $adminCount = User::whereHas('role', function ($q) {

            $q->whereRaw(
                'LOWER(name) = ?',
                ['admin']
            );

        })->count();

        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */

        $sections = AcademicSection::orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        $roles = \App\Models\Role::orderBy('name')->get();

        $departments = collect();

        return view('admin.users.index', compact(
            'users',
            'sections',
            'roles',
            'departments',
            'activeUsers',
            'inactiveUsers',
            'totalUsers',
            'studentCount',
            'teacherCount',
            'adminCount'
        ));
    }


    /**
     * Show create user form.
     */
    public function create()
    {
        $roles = \App\Models\Role::orderBy('name')->get();

        $sections = AcademicSection::orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        $departments = collect();

        return view('admin.users.create', compact(
            'roles',
            'sections',
            'departments'
        ));
    }


    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'id_number' => [
                'required',
                'string',
                'max:50',
                'unique:users,id_number',
            ],
            'first_name' => [
                'required',
                'string',
                'max:255',
],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'parent_phone_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'grade_level' => [
    'required',
    'string',
    'max:50',
],

'strand' => [
    'required',
    'string',
    'max:100',
],

            'nfc_tag_id' => [
                'nullable',
                'string',
                'max:100',
                'unique:nfc_cards,tag_id',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */
$user = User::create([

    'id_number' => $validated['id_number'],
    'role_id' => $validated['role_id'],
    'first_name' => $validated['first_name'],
    'last_name' => $validated['last_name'],
    'email' => $validated['email'],

    'password' => Hash::make(
        $validated['password']
    ),

    'phone_number' =>
        $validated['phone_number'] ?? null,

    'parent_phone_number' =>
        $validated['parent_phone_number'] ?? null,

    'grade_level' =>
        $validated['grade_level'],

    'strand' =>
        $validated['strand'],

    'is_active' => true,

]);


        /*
        |--------------------------------------------------------------------------
        | Save Grade / Section
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['grade_section'])) {

            $user->sections()->sync([
                $validated['grade_section']
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Save NFC Tag
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['nfc_tag_id'])) {

            NfcCard::create([

                'user_id' => $user->id,

                'tag_id' => $validated['nfc_tag_id'],

            ]);

        }


        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User created successfully.'
            );
    }


    /**
     * Show edit user form.
     */
    public function edit(User $user)
    {
        $user->load([
            'role',
            'nfcCard',
            'sections'
        ]);

        $roles = \App\Models\Role::orderBy('name')->get();

        $sections = AcademicSection::orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        $departments = collect();

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'sections',
            'departments'
        ));
    }


    /**
     * Update user.
     */
    public function update(
        Request $request,
        User $user
    ) {
        $validated = $request->validate([

            'id_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'id_number')
                    ->ignore($user->id),
            ],

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'parent_phone_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'grade_section' => [
                'nullable',
                'exists:academic_sections,id',
            ],

            'nfc_tag_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('nfc_cards', 'tag_id')
                    ->ignore(
                        optional($user->nfcCard)->id
                    ),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update User
        |--------------------------------------------------------------------------
        */

        $user->update([

            'id_number' => $validated['id_number'],

            'role_id' => $validated['role_id'],

            'first_name' => $validated['first_name'],

            'last_name' => $validated['last_name'],

            'email' => $validated['email'],

            'phone_number' =>
                $validated['phone_number'] ?? null,

            'parent_phone_number' =>
                $validated['parent_phone_number'] ?? null,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {

            $user->update([

                'password' => Hash::make(
                    $validated['password']
                ),

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Update Grade / Section
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['grade_section'])) {

            $user->sections()->sync([
                $validated['grade_section']
            ]);

        } else {

            $user->sections()->detach();

        }


        /*
        |--------------------------------------------------------------------------
        | Update NFC
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['nfc_tag_id'])) {

            NfcCard::updateOrCreate(

                [
                    'user_id' => $user->id
                ],

                [
                    'tag_id' =>
                        $validated['nfc_tag_id']
                ]

            );

        } else {

            if ($user->nfcCard) {

                $user->nfcCard->delete();

            }

        }


        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User updated successfully.'
            );
    }


    /**
     * Delete user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User deleted successfully.'
            );
    }
}