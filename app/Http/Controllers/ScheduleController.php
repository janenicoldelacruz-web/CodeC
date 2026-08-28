<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\User;
use App\Models\AcademicSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // 1. Fetch Teachers (Role ID 2 or teacher/faculty string)
        $teachers = User::where(function ($q) {
            $cols = Schema::getColumnListing('users');
            if (in_array('role_id', $cols)) $q->where('role_id', 2);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'teacher')->orWhere('role', 'faculty');
            }
        })->orderBy('first_name')->get();

        // 2. Determine column names dynamically to avoid SQL errors
        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];
        $sectionCol = in_array('section_id', $schedCols) ? 'section_id' : (in_array('section', $schedCols) ? 'section' : null);
        $subjectCol = in_array('subject_name', $schedCols) ? 'subject_name' : (in_array('subject_id', $schedCols) ? 'subject_id' : null);

        // 3. Query Schedules with Teacher and Subject Relationships Eager-Loaded
        $query = ClassSchedule::with(['teacher', 'subjectRecord'])->latest('id');

        if ($request->filled('grade_level') && in_array('grade_level', $schedCols)) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('strand') && in_array('strand', $schedCols)) {
            $query->where('strand', $request->strand);
        }

        if ($request->filled('section') && $sectionCol) {
            $query->where($sectionCol, $request->section);
        }

        $dayFilterCol = in_array('day_of_week', $schedCols) ? 'day_of_week' : (in_array('day', $schedCols) ? 'day' : null);
        if ($request->filled('day') && $dayFilterCol) {
            $query->where($dayFilterCol, $request->day);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search, $schedCols, $sectionCol, $subjectCol) {
                if ($subjectCol) {
                    $q->where($subjectCol, 'like', "%{$search}%");
                }
                if (in_array('subject_code', $schedCols)) {
                    $q->orWhere('subject_code', 'like', "%{$search}%");
                }
                if ($sectionCol) {
                    $q->orWhere($sectionCol, 'like', "%{$search}%");
                }
                $q->orWhereHas('teacher', function ($t) use ($search) {
                    $t->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        $schedules = $query->paginate(12);

        // 4. Safe Dynamic KPIs
        if (class_exists(AcademicSection::class) && Schema::hasTable('academic_sections')) {
            $totalSections = AcademicSection::count();
        } elseif ($sectionCol) {
            $totalSections = ClassSchedule::distinct($sectionCol)->count($sectionCol);
        } else {
            $totalSections = 0;
        }

        $totalSubjects = $subjectCol ? ClassSchedule::distinct($subjectCol)->count($subjectCol) : 0;
        $assignedFacultyCount = in_array('teacher_id', $schedCols) 
            ? ClassSchedule::whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id') 
            : 0;

        return view('admin.schedules', compact(
            'schedules',
            'teachers',
            'totalSections',
            'totalSubjects',
            'assignedFacultyCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => ['required', 'string', 'max:255'],
            'subject_code' => ['nullable', 'string', 'max:50'],
            'teacher_id'   => ['required', 'exists:users,id'],
            'grade_level'  => ['required', 'string', 'max:50'],
            'strand'       => ['required', 'string', 'max:50'],
            'section'      => ['required', 'string', 'max:100'],
            'day'          => ['required', 'string', 'max:50'],
            'start_time'   => ['required'],
            'end_time'     => ['required'],
        ]);

        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];
        $data = [];

        // 1. Fully Dynamic Subject Resolution
        if (in_array('subject_id', $schedCols)) {
            $subjectId = 1;
            if (Schema::hasTable('subjects')) {
                $subCols = Schema::getColumnListing('subjects');
                $nameCol = null;
                foreach (['name', 'title', 'subject_name', 'description'] as $col) {
                    if (in_array($col, $subCols)) {
                        $nameCol = $col;
                        break;
                    }
                }

                if ($nameCol) {
                    $existingSub = DB::table('subjects')->where($nameCol, $request->subject_name)->first();
                    if ($existingSub) {
                        $subjectId = $existingSub->id;
                    } else {
                        $insertData = [$nameCol => $request->subject_name];
                        $generatedCode = $request->subject_code ?? 'SUBJ-' . rand(100, 999);

                        if (in_array('code', $subCols)) $insertData['code'] = $generatedCode;
                        if (in_array('subject_code', $subCols)) $insertData['subject_code'] = $generatedCode;
                        if (in_array('created_at', $subCols)) $insertData['created_at'] = now();
                        if (in_array('updated_at', $subCols)) $insertData['updated_at'] = now();
                        
                        $subjectId = DB::table('subjects')->insertGetId($insertData);
                    }
                }
            }
            $data['subject_id'] = $subjectId;
        }

        if (in_array('subject_name', $schedCols)) {
            $data['subject_name'] = $request->subject_name;
        } elseif (in_array('subject', $schedCols)) {
            $data['subject'] = $request->subject_name;
        }

        // 2. Fully Dynamic Section Resolution
        if (in_array('section_id', $schedCols)) {
            $sectionId = 1;
            if (Schema::hasTable('academic_sections') || Schema::hasTable('sections')) {
                $tableName = Schema::hasTable('academic_sections') ? 'academic_sections' : 'sections';
                $secCols = Schema::getColumnListing($tableName);
                $secNameCol = in_array('name', $secCols) ? 'name' : (in_array('section_name', $secCols) ? 'section_name' : null);

                if ($secNameCol) {
                    $sec = DB::table($tableName)->where($secNameCol, $request->section)->first();
                    if ($sec) {
                        $sectionId = $sec->id;
                    } else {
                        $secInsert = [$secNameCol => $request->section];
                        if (in_array('grade_level', $secCols)) $secInsert['grade_level'] = $request->grade_level;
                        if (in_array('strand', $secCols)) $secInsert['strand'] = $request->strand;
                        if (in_array('created_at', $secCols)) $secInsert['created_at'] = now();
                        if (in_array('updated_at', $secCols)) $secInsert['updated_at'] = now();

                        $sectionId = DB::table($tableName)->insertGetId($secInsert);
                    }
                }
            }
            $data['section_id'] = is_numeric($sectionId) ? $sectionId : 1;
        } elseif (in_array('section', $schedCols)) {
            $data['section'] = $request->section;
        }

        // 3. Dynamic Day Column Mapping
        if (in_array('day_of_week', $schedCols)) {
            $data['day_of_week'] = $request->day;
        } elseif (in_array('day', $schedCols)) {
            $data['day'] = $request->day;
        } elseif (in_array('days', $schedCols)) {
            $data['days'] = $request->day;
        }

        // 4. Safe Standard Field Mappings
        $mappings = [
            'subject_code' => $request->subject_code,
            'teacher_id'   => $request->teacher_id,
            'grade_level'  => $request->grade_level,
            'strand'       => $request->strand,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
        ];

        foreach ($mappings as $col => $val) {
            if (in_array($col, $schedCols)) {
                $data[$col] = $val;
            }
        }

        // 5. Bulletproof Dynamic Academic Period ID Resolution & Creation
        if (in_array('academic_period_id', $schedCols)) {
            $activePeriodId = null;
            if (Schema::hasTable('academic_periods')) {
                $periodCols = Schema::getColumnListing('academic_periods');
                
                $activePeriodId = DB::table('academic_periods')->where('is_active', 1)->value('id') 
                                ?? DB::table('academic_periods')->value('id');

                if (!$activePeriodId) {
                    $insertPeriod = [];
                    if (in_array('name', $periodCols)) $insertPeriod['name'] = 'A.Y. ' . date('Y') . '-' . (date('Y') + 1);
                    if (in_array('school_year', $periodCols)) $insertPeriod['school_year'] = date('Y') . '-' . (date('Y') + 1);
                    if (in_array('semester', $periodCols)) $insertPeriod['semester'] = '1st Semester';
                    if (in_array('is_active', $periodCols)) $insertPeriod['is_active'] = 1;
                    if (in_array('created_at', $periodCols)) $insertPeriod['created_at'] = now();
                    if (in_array('updated_at', $periodCols)) $insertPeriod['updated_at'] = now();

                    $activePeriodId = DB::table('academic_periods')->insertGetId($insertPeriod);
                }
            }
            $data['academic_period_id'] = $activePeriodId ?? 1;
        }

        ClassSchedule::create($data);

        return redirect()->route('admin.schedules')->with('success', 'Class schedule created successfully!');
    }

    public function update(Request $request, $id)
    {
        $schedule = ClassSchedule::findOrFail($id);

        $request->validate([
            'subject_name' => ['required', 'string', 'max:255'],
            'subject_code' => ['nullable', 'string', 'max:50'],
            'teacher_id'   => ['required', 'exists:users,id'],
            'grade_level'  => ['required', 'string', 'max:50'],
            'strand'       => ['required', 'string', 'max:50'],
            'section'      => ['required', 'string', 'max:100'],
            'day'          => ['required', 'string', 'max:50'],
            'start_time'   => ['required'],
            'end_time'     => ['required'],
        ]);

        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];
        $data = [];

        if (in_array('subject_id', $schedCols)) {
            $subjectId = 1;
            if (Schema::hasTable('subjects')) {
                $subCols = Schema::getColumnListing('subjects');
                $nameCol = in_array('name', $subCols) ? 'name' : (in_array('subject_name', $subCols) ? 'subject_name' : null);
                if ($nameCol) {
                    $existingSub = DB::table('subjects')->where($nameCol, $request->subject_name)->first();
                    if ($existingSub) {
                        $subjectId = $existingSub->id;
                    } else {
                        $insertData = [$nameCol => $request->subject_name];
                        $generatedCode = $request->subject_code ?? 'SUBJ-' . rand(100, 999);
                        if (in_array('code', $subCols)) $insertData['code'] = $generatedCode;
                        if (in_array('subject_code', $subCols)) $insertData['subject_code'] = $generatedCode;
                        if (in_array('created_at', $subCols)) $insertData['created_at'] = now();
                        if (in_array('updated_at', $subCols)) $insertData['updated_at'] = now();
                        $subjectId = DB::table('subjects')->insertGetId($insertData);
                    }
                }
            }
            $data['subject_id'] = $subjectId;
        }

        if (in_array('subject_name', $schedCols)) $data['subject_name'] = $request->subject_name;
        elseif (in_array('subject', $schedCols)) $data['subject'] = $request->subject_name;

        if (in_array('day_of_week', $schedCols)) $data['day_of_week'] = $request->day;
        elseif (in_array('day', $schedCols)) $data['day'] = $request->day;

        $mappings = [
            'subject_code' => $request->subject_code,
            'teacher_id'   => $request->teacher_id,
            'grade_level'  => $request->grade_level,
            'strand'       => $request->strand,
            'section'      => $request->section,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
        ];

        foreach ($mappings as $col => $val) {
            if (in_array($col, $schedCols)) {
                $data[$col] = $val;
            }
        }

        $schedule->update($data);

        return redirect()->route('admin.schedules')->with('success', 'Class schedule updated successfully!');
    }

    public function destroy($id)
    {
        ClassSchedule::findOrFail($id)->delete();
        return redirect()->route('admin.schedules')->with('success', 'Class schedule deleted successfully!');
    }

    public function clearAll()
    {
        ClassSchedule::truncate();
        return redirect()->route('admin.schedules')->with('success', 'All schedules have been cleared.');
    }
}