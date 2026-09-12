<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminSectionController extends Controller
{
    public function index()
    {
        $activeSchoolYear = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_school_year')->value('value') ?? '2025-2026') 
            : '2025-2026';

        $activeSemester = Schema::hasTable('settings') 
            ? (DB::table('settings')->where('key', 'active_semester')->value('value') ?? '1st Semester') 
            : '1st Semester';

        // Flat collection lang muna para sigurado at walang collection property error
        $sections = Schema::hasTable('academic_sections') 
            ? DB::table('academic_sections')->orderBy('id', 'desc')->get() 
            : collect([]);

        return view('admin.sections.index', compact('sections', 'activeSchoolYear', 'activeSemester'));
    }

    public function storeSection(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string|max:50',
            'section_name' => 'required|string|max:100',
            'strand' => 'nullable|string|max:50'
        ]);

        if (Schema::hasTable('academic_sections')) {
            DB::table('academic_sections')->insert([
                'grade_level' => ucwords(strtolower(trim($request->grade_level))),
                'section_name' => trim($request->section_name),
                'strand' => strtoupper(trim($request->strand)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return back()->with('success', 'Class section successfully added.');
    }

    public function destroySection($id)
    {
        if (Schema::hasTable('academic_sections')) {
            DB::table('academic_sections')->where('id', $id)->delete();
        }
        return back()->with('success', 'Class section successfully removed.');
    }
}