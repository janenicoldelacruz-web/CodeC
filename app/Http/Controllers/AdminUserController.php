<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AcademicSection;
use App\Models\NfcCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function index()
    {
        $users    = User::with('role')->latest()->paginate(10);
        $roles    = Role::all();
        $sections = class_exists(AcademicSection::class) ? AcademicSection::all() : collect();

        // Statistical Counters
        $totalUsers   = User::count();
        $studentCount = User::where('role_id', 3)->count();
        $teacherCount = User::where('role_id', 2)->count();
        $adminCount   = User::where('role_id', 1)->count();
        $activeUsers  = User::where('is_active', 1)->count();

        return view('admin.users.index', compact(
            'users',
            'roles',
            'sections',
            'totalUsers',
            'studentCount',
            'teacherCount',
            'adminCount',
            'activeUsers'
        ));
    }

    public function create()
    {
        $roles    = Role::all();
        $sections = class_exists(AcademicSection::class) ? AcademicSection::all() : collect();

        return view('admin.users.create', compact('roles', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id'             => ['required', 'exists:roles,id'],
            'id_number'           => ['nullable', 'string', 'max:50', 'unique:users,id_number'],
            'first_name'          => ['required', 'string', 'max:255'],
            'last_name'           => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'            => ['required', 'string', 'min:6', 'confirmed'],
            'phone_number'        => ['nullable', 'string', 'max:20'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'strand'              => ['nullable', 'string', 'max:20'],
            'nfc_tag_id'          => ['nullable', 'string', 'max:100', 'unique:nfc_cards,tag_id'],
            'academic_section_id' => ['nullable', 'exists:academic_sections,id'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'role_id'             => $request->role_id,
                'id_number'           => $request->id_number,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'password'            => Hash::make($request->password),
                'phone_number'        => $request->phone_number,
                'parent_phone_number' => $request->parent_phone_number,
                'strand'              => $request->strand,
                'is_active'           => 1,
            ]);

            if (!empty($request->nfc_tag_id)) {
                NfcCard::create([
                    'user_id' => $user->id,
                    'tag_id'  => $request->nfc_tag_id,
                ]);
            }

            if (!empty($request->academic_section_id)) {
                DB::table('section_student')->insert([
                    'student_id' => $user->id,
                    'section_id' => $request->academic_section_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'User successfully created!');
    }
}