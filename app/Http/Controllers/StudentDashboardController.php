<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Retrieve Registration Details from users / students / academic_sections
        $gradeLevel = null;
        $strand     = null;
        $section    = null;

        // Check columns directly in the users record
        $gradeLevel = $user->grade_level ?? $user->grade ?? $user->year_level ?? null;
        $strand     = $user->strand ?? $user->track ?? $user->course ?? $user->program ?? null;
        $section    = $user->section ?? $user->section_name ?? null;

        // Check separate students / student_profiles table if it exists
        if ((empty($gradeLevel) || empty($section)) && Schema::hasTable('students')) {
            $studentProfile = DB::table('students')
                ->where('user_id', $user->id)
                ->orWhere('id', $user->id)
                ->first();

            if ($studentProfile) {
                $gradeLevel = $gradeLevel ?? $studentProfile->grade_level ?? $studentProfile->grade ?? $studentProfile->year_level ?? null;
                $strand     = $strand ?? $studentProfile->strand ?? $studentProfile->track ?? $studentProfile->course ?? null;
                $section    = $section ?? $studentProfile->section ?? $studentProfile->section_name ?? null;
                if (!empty($studentProfile->section_id) && empty($user->section_id)) {
                    $user->section_id = $studentProfile->section_id;
                }
            }
        }

        // Check linked academic_sections table via section_id
        if ((empty($section) || empty($gradeLevel)) && !empty($user->section_id) && Schema::hasTable('academic_sections')) {
            $sectionRow = DB::table('academic_sections')->where('id', $user->section_id)->first();
            if ($sectionRow) {
                $section    = $section ?? $sectionRow->name ?? $sectionRow->section_name ?? $sectionRow->section ?? null;
                $gradeLevel = $gradeLevel ?? $sectionRow->grade_level ?? $sectionRow->grade ?? $sectionRow->year_level ?? null;
                $strand     = $strand ?? $sectionRow->strand ?? $sectionRow->track ?? null;
            }
        }

        // Attach resolved registration data back to $student object
        $student = clone $user;
        $student->grade_level = $gradeLevel ?? 'Grade 11';
        $student->strand      = $strand ?? 'Information & Communications Technology';
        $student->section     = $section ?? 'Diamond';

        // 2. Fetch Class Schedules matching the student's section
        $schedules = collect();

        if (Schema::hasTable('class_schedules')) {
            $query = DB::table('class_schedules');

            // Resolve Subjects Table Column Names
            $subjectNameCol = null;
            $subjectCodeCol = null;
            if (Schema::hasTable('subjects')) {
                foreach (['name', 'subject_name', 'title'] as $col) {
                    if (Schema::hasColumn('subjects', $col)) {
                        $subjectNameCol = $col;
                        break;
                    }
                }
                foreach (['code', 'subject_code'] as $col) {
                    if (Schema::hasColumn('subjects', $col)) {
                        $subjectCodeCol = $col;
                        break;
                    }
                }
                $query->leftJoin('subjects', 'class_schedules.subject_id', '=', 'subjects.id');
            }

            // Resolve Academic Sections Table Column Names
            $secNameCol = null;
            $secGradeCol = null;
            $secStrandCol = null;
            if (Schema::hasTable('academic_sections')) {
                foreach (['section_name', 'name', 'section', 'title'] as $col) {
                    if (Schema::hasColumn('academic_sections', $col)) {
                        $secNameCol = $col;
                        break;
                    }
                }
                foreach (['grade_level', 'grade', 'year_level'] as $col) {
                    if (Schema::hasColumn('academic_sections', $col)) {
                        $secGradeCol = $col;
                        break;
                    }
                }
                foreach (['strand', 'track_strand', 'track'] as $col) {
                    if (Schema::hasColumn('academic_sections', $col)) {
                        $secStrandCol = $col;
                        break;
                    }
                }
                $query->leftJoin('academic_sections', 'class_schedules.section_id', '=', 'academic_sections.id');
            }

            // Teacher Join
            if (Schema::hasTable('users') && Schema::hasColumn('class_schedules', 'teacher_id')) {
                $query->leftJoin('users as teachers', 'class_schedules.teacher_id', '=', 'teachers.id');
            }

            // Select Columns
            $selects = [
                'class_schedules.id',
                'class_schedules.day_of_week as day',
                'class_schedules.start_time',
                'class_schedules.end_time',
            ];

            if (Schema::hasColumn('class_schedules', 'room')) {
                $selects[] = 'class_schedules.room';
            }

            $selects[] = $subjectNameCol ? "subjects.{$subjectNameCol} as subject_name" : DB::raw("'General Course' as subject_name");
            $selects[] = $subjectCodeCol ? "subjects.{$subjectCodeCol} as subject_code" : DB::raw("'NO-CODE' as subject_code");
            $selects[] = $secNameCol ? "academic_sections.{$secNameCol} as section_name" : DB::raw("'Assigned Section' as section_name");

            if (Schema::hasTable('users')) {
                $selects[] = 'teachers.first_name as teacher_first_name';
                $selects[] = 'teachers.last_name as teacher_last_name';
                $selects[] = 'teachers.email as teacher_email';
            }

            $query->select($selects);

            // Filter Schedules by Student Section ID or Name
            if (!empty($student->section_id) && Schema::hasColumn('class_schedules', 'section_id')) {
                $query->where('class_schedules.section_id', $student->section_id);
            } else {
                if (!empty($student->section) && $secNameCol) {
                    $query->where("academic_sections.{$secNameCol}", 'like', '%' . $student->section . '%');
                }
                if (!empty($student->grade_level) && $secGradeCol) {
                    $query->where("academic_sections.{$secGradeCol}", $student->grade_level);
                }
            }

            // Sort by day and time
            if (Schema::hasColumn('class_schedules', 'day_of_week')) {
                $query->orderByRaw("FIELD(class_schedules.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Mon / Wed', 'Tue / Thu')");
            }
            if (Schema::hasColumn('class_schedules', 'start_time')) {
                $query->orderBy('class_schedules.start_time', 'asc');
            }

            $schedules = $query->get()->map(function ($schedule) {
                $schedule->teacher = !empty($schedule->teacher_first_name) ? (object)[
                    'first_name' => $schedule->teacher_first_name,
                    'last_name'  => $schedule->teacher_last_name,
                    'email'      => $schedule->teacher_email,
                ] : null;

                $schedule->time_slot = (!empty($schedule->start_time) && !empty($schedule->end_time))
                    ? date('g:i A', strtotime($schedule->start_time)) . ' - ' . date('g:i A', strtotime($schedule->end_time))
                    : 'Schedule TBA';

                return $schedule;
            });
        }

        // 3. Faculty Evaluation Status Check
        $isEvaluationOpen = false;
        if (Schema::hasTable('system_settings')) {
            $status = DB::table('system_settings')->where('key', 'evaluation_status')->value('value');
            $isEvaluationOpen = in_array(strtolower((string)$status), ['open', 'active', '1', 'true']);
        } elseif (Schema::hasTable('settings')) {
            $status = DB::table('settings')->where('key', 'evaluation_status')->value('value');
            $isEvaluationOpen = in_array(strtolower((string)$status), ['open', 'active', '1', 'true']);
        }

        return view('student.dashboard', compact('student', 'schedules', 'isEvaluationOpen'));
    }
}