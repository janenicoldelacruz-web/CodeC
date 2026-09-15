<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class AdminSchoolYearController extends Controller
{
    public function index()
    {
        // 1. Siguraduhing laging may tamang default na values kung sakaling blanko o may maling data sa settings
        $sySetting = DB::table('settings')->where('key', 'active_school_year')->first();
        if (!$sySetting || strlen($sySetting->value) > 9 || !str_contains($sySetting->value, '-')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'active_school_year'],
                ['value' => '2026-2027', 'updated_at' => now()]
            );
        }

        $semSetting = DB::table('settings')->where('key', 'active_semester')->first();
        if (!$semSetting) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'active_semester'],
                ['value' => '1ST SEMESTER', 'updated_at' => now()]
            );
        }

        $activeSchoolYear = DB::table('settings')->where('key', 'active_school_year')->value('value');
        $activeSemester = DB::table('settings')->where('key', 'active_semester')->value('value');
        $totalLogs = Schema::hasTable('attendance_logs') ? DB::table('attendance_logs')->count() : 0;

        return view('admin.school-year.index', compact('activeSchoolYear', 'activeSemester', 'totalLogs'));
    }

    public function update(Request $request)
    {
        // 1. I-validate ang pormat ng inputs at password field
        $request->validate([
            'academic_year'  => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester'       => ['required', 'string', 'max:100'],
            'admin_password' => ['required', 'string']
        ], [
            'academic_year.regex' => 'The school year must strictly follow the YYYY-YYYY format (e.g., 2026-2027).'
        ]);

        $user = auth()->user();

        // 2. Ligtas na Password Check (iniiwasan ang Bcrypt exception kung plain text ang nasa DB)
        $isPasswordValid = false;
        
        if ($user && $user->password) {
            // Kung sakaling plain text ang nakaimbak sa database
            if ($request->admin_password === $user->password) {
                $isPasswordValid = true;
            } else {
                // Subukan ang Hash::check kung naka-encrypt ito nang tama
                try {
                    if (\Illuminate\Support\Facades\Hash::check($request->admin_password, $user->password)) {
                        $isPasswordValid = true;
                    }
                } catch (\Exception $e) {
                    $isPasswordValid = false;
                }
            }
        }

        if (!$isPasswordValid) {
            return back()->with('error', 'Incorrect admin password. Changes not saved.');
        }

        // 3. I-save sa database kapag tama ang password
        if (Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'active_school_year'],
                ['value' => $request->academic_year, 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'active_semester'],
                ['value' => strtoupper($request->semester), 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Active school year successfully updated to A.Y. ' . $request->academic_year . ' — ' . strtoupper($request->semester));
    }

    public function reset()
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'active_school_year'],
                ['value' => '2026-2027', 'updated_at' => now()]
            );
            DB::table('settings')->updateOrInsert(
                ['key' => 'active_semester'],
                ['value' => '1ST SEMESTER', 'updated_at' => now()]
            );
        }

        return back()->with('success', 'School year configuration reset to default (2026-2027).');
    }
}