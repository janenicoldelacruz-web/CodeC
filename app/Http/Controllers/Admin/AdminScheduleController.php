<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\User;
use App\Models\AcademicSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AdminScheduleController extends Controller
{
    public function index(Request $request)
    {
        $teachers = User::where(function ($q) {
            $cols = Schema::getColumnListing('users');
            if (in_array('role_id', $cols)) $q->where('role_id', 2);
            if (in_array('role', $cols)) {
                $q->orWhere('role', 'teacher')->orWhere('role', 'faculty');
            }
        })->orderBy('first_name')->get();

        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];
        $sectionCol = in_array('section_id', $schedCols) ? 'section_id' : (in_array('section', $schedCols) ? 'section' : null);
        $subjectCol = in_array('subject_name', $schedCols) ? 'subject_name' : (in_array('subject_id', $schedCols) ? 'subject_id' : null);

        $query = ClassSchedule::with(['teacher', 'subjectRecord', 'academicSection'])->latest('id');

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

        return view('admin.schedules.index', compact(
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
            'grade_level'  => ['required', 'string', 'max:50'], // <-- IDAGDAG ITO
            'strand'       => ['required', 'string', 'max:50'],
            'section'      => ['required', 'string', 'max:100'],
            'day'          => ['required', 'string', 'max:50'],
            'start_time'   => ['required'],
            'end_time'     => ['required'],
        ]);

        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];
        
        // --- TEACHER OVERLAP / CONFLICT VALIDATION ---
        $dayColName = in_array('day_of_week', $schedCols) ? 'day_of_week' : 'day';
        $conflict = ClassSchedule::where('teacher_id', $request->teacher_id)
            ->where($dayColName, $request->day)
            ->where(function ($query) use ($request) {
                // Check if time ranges overlap: (StartA < EndB) and (EndA > StartB)
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['teacher_id' => 'Conflict Error: The selected faculty member already has an overlapping class schedule on this day and time slot.'])->withInput();
        }
        // ---------------------------------------------

        $data = [];

        if (in_array('subject_id', $schedCols)) {
            $subjectId = 1;
            if (Schema::hasTable('subjects')) {
                $subCols = Schema::getColumnListing('subjects');
                $nameCol = null;
                foreach (['name', 'title', 'subject_name', 'description'] as $col) {
                    if (in_array($col, $subCols)) { $nameCol = $col; break; }
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

        if (in_array('day_of_week', $schedCols)) {
            $data['day_of_week'] = $request->day;
        } elseif (in_array('day', $schedCols)) {
            $data['day'] = $request->day;
        } elseif (in_array('days', $schedCols)) {
            $data['days'] = $request->day;
        }

        if (in_array('section_id', $schedCols)) {
            $sectionRecord = DB::table('academic_sections')->where('section_name', $request->section)->first();
            $data['section_id'] = $sectionRecord ? $sectionRecord->id : 1;
        }

        $mappings = [
            'subject_code' => $request->subject_code,
            'teacher_id'   => $request->teacher_id,
            'grade_level'  => $request->grade_level, // <-- IDAGDAG ITO
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

        return redirect()->route('admin.schedules.index')->with('success', 'Class schedule created successfully!');
    }

    public function update(Request $request, $id)
    {
        $schedule = ClassSchedule::findOrFail($id);

      $request->validate([
            'subject_name' => ['required', 'string', 'max:255'],
            'subject_code' => ['nullable', 'string', 'max:50'],
            'teacher_id'   => ['required', 'exists:users,id'],
            'grade_level'  => ['required', 'string', 'max:50'], // <-- IDAGDAG ITO
            'strand'       => ['required', 'string', 'max:50'],
            'section'      => ['required', 'string', 'max:100'],
            'day'          => ['required', 'string', 'max:50'],
            'start_time'   => ['required'],
            'end_time'     => ['required'],
        ]);

        $schedCols = Schema::hasTable('class_schedules') ? Schema::getColumnListing('class_schedules') : [];

        // --- TEACHER OVERLAP / CONFLICT VALIDATION FOR UPDATE ---
        $dayColName = in_array('day_of_week', $schedCols) ? 'day_of_week' : 'day';
        $conflict = ClassSchedule::where('teacher_id', $request->teacher_id)
            ->where('id', '!=', $id) // I-exlude ang kasalukuyang schedule na ini-edit
            ->where($dayColName, $request->day)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['teacher_id' => 'Conflict Error: The selected faculty member already has an overlapping class schedule on this day and time slot.'])->withInput();
        }
        // --------------------------------------------------------

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

        if (in_array('section_id', $schedCols)) {
            $sectionRecord = DB::table('academic_sections')->where('section_name', $request->section)->first();
            $data['section_id'] = $sectionRecord ? $sectionRecord->id : 1;
        }
$mappings = [
            'subject_code' => $request->subject_code,
            'teacher_id'   => $request->teacher_id,
            'grade_level'  => $request->grade_level, // <-- IDAGDAG ITO
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

        return redirect()->route('admin.schedules.index')->with('success', 'Class schedule updated successfully!');
    }

   public function import(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
    ]);

    $file = $request->file('file');
    $path = $file->getRealPath();

    $data = array_map('str_getcsv', file($path));
    $header = array_shift($data); // Alisin ang header row

    $importedCount = 0;

    foreach ($data as $row) {
        if (count($row) < 9) continue;

        [$subjectName, $subjectCode, $teacherId, $gradeLevel, $strand, $section, $day, $startTime, $endTime] = $row;

        ClassSchedule::create([
            'subject_name' => trim($subjectName),
            'subject_code' => trim($subjectCode),
            'teacher_id'   => trim($teacherId),
            'grade_level'  => trim($gradeLevel),
            'strand'       => trim($strand),
            'section'      => trim($section),
            'day'          => trim($day),
            'start_time'   => trim($startTime),
            'end_time'     => trim($endTime),
        ]);

        $importedCount++;
    }

    return redirect()->route('admin.schedules.index')->with('success', "Successfully imported {$importedCount} class schedules!");
}

public function matrix()
{
    $schedules = ClassSchedule::with(['teacher', 'academicSection'])->get();
    return view('admin.schedules.matrix', compact('schedules'));
}
    public function destroy($id)
    {
        ClassSchedule::findOrFail($id)->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Class schedule deleted successfully!');
    }

    public function clearAll()
    {
        ClassSchedule::truncate();
        return redirect()->route('admin.schedules.index')->with('success', 'All schedules have been cleared.');
    }
}