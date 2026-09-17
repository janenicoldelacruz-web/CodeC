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
            'grade_level'  => 'required|string|max:50',
            'section_name' => 'required|string|max:100',
            'strand'       => 'nullable|string|max:50'
        ]);

        if (Schema::hasTable('academic_sections')) {
            DB::table('academic_sections')->insert([
                'grade_level'  => ucwords(strtolower(trim($request->grade_level))),
                'section_name' => trim($request->section_name),
                'strand'       => $request->filled('strand') ? strtoupper(trim($request->strand)) : null,
                'created_at'   => now(),
                'updated_at'   => now()
            ]);
        }

        return back()->with('success', 'Class section successfully added.');
    }

   public function updateSection(Request $request, $id)
    {
        $request->validate([
            'grade_level'  => 'required|string|max:50',
            'section_name' => 'required|string|max:100',
            'strand'       => 'nullable|string|max:50'
        ]);

        if (Schema::hasTable('academic_sections')) {
            // 1. Kunin muna ang lumang detalye ng section bago i-update
            $oldSection = DB::table('academic_sections')->where('id', $id)->first();

            $newGradeLevel = ucwords(strtolower(trim($request->grade_level)));
            $newSectionName = trim($request->section_name);
            $newStrand = $request->filled('strand') ? strtoupper(trim($request->strand)) : null;

            // 2. I-update ang section sa academic_sections table
            DB::table('academic_sections')->where('id', $id)->update([
                'grade_level'  => $newGradeLevel,
                'section_name' => $newSectionName,
                'strand'       => $newStrand,
                'updated_at'   => now()
            ]);

            // 3. PROPAGATION SA USER MANAGEMENT (Pinanatili ang strand, tinanggal ang track)
            if ($oldSection) {
                \App\Models\User::where('section', $oldSection->section_name)
                    ->where('grade_level', $oldSection->grade_level)
                    ->update([
                        'grade_level' => $newGradeLevel,
                        'section'     => $newSectionName,
                        'strand'      => $newStrand
                    ]);
            }
        }

        return back()->with('success', 'Class section and its user placements successfully updated.');
    }

    public function destroySection($id)
    {
        if (Schema::hasTable('academic_sections')) {
            $section = DB::table('academic_sections')->where('id', $id)->first();

            if ($section) {
                // Opsyonal: I-clear o i-set as null ang section ng mga users na nakatali dito para walang "orphan placement"
                \App\Models\User::where('section', $section->section_name)
                    ->where('grade_level', $section->grade_level)
                    ->update([
                        'section' => null,
                        'grade_level' => null,
                        'strand' => null
                    ]);

                // Burahin na ang section
                DB::table('academic_sections')->where('id', $id)->delete();
            }
        }
        return back()->with('success', 'Class section successfully removed and user placements cleared.');
    }
}