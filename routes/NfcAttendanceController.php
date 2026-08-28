<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NfcCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class NfcAttendanceController extends Controller
{
    /**
     * Display the Real-Time Attendance Tap Station (Kiosk)
     */
    public function kioskView()
    {
        return view('teacher.kiosk');
    }

    /**
     * Process NFC Card Tap from ACR122U Reader
     */
    public function handleTap(Request $request)
    {
        $request->validate([
            'card_uid' => 'required|string',
        ]);

        $cardUid = strtoupper(trim($request->card_uid));

        // 1. Hanapin ang Student gamit ang NFC UID
        $user = null;

        // A. Hanapin muna sa nfc_cards table kung meron
        if (Schema::hasTable('nfc_cards')) {
            $nfcRecord = DB::table('nfc_cards')->where('tag_id', $cardUid)->first();
            if ($nfcRecord) {
                $user = User::find($nfcRecord->user_id);
            }
        }

        // B. Fallback: Hanapin sa id_number o email kung simulated UID
        if (!$user) {
            $user = User::where('id_number', $cardUid)
                        ->orWhere('id_number', 'like', "%{$cardUid}%")
                        ->first();
        }

        // Kung hindi rehistrado ang card
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Unregistered NFC Card (UID: {$cardUid}). Please register this card in Admin.",
                'card_uid' => $cardUid,
            ], 404);
        }

        // 2. Kalkulahin ang Time at Status (Cutoff: 8:15 AM)
        $now = Carbon::now();
        $cutoffTime = Carbon::today()->setHour(8)->setMinute(15)->setSecond(0);

        $status = $now->greaterThan($cutoffTime) ? 'LATE' : 'ON-TIME';

        // 3. I-save sa attendance_logs table
        if (Schema::hasTable('attendance_logs')) {
            $foreignKey = Schema::hasColumn('attendance_logs', 'student_id') ? 'student_id' : 'user_id';

            DB::table('attendance_logs')->insert([
                $foreignKey       => $user->id,
                'attendance_date' => Carbon::today()->toDateString(),
                'time_in'         => $now,
                'status'          => $status,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Attendance successfully logged!',
            'student'   => [
                'id'        => $user->id,
                'name'      => $user->first_name . ' ' . $user->last_name,
                'id_number' => $user->id_number ?? '00' . (230 + $user->id),
                'strand'    => $user->strand ?? 'SHS Department',
                'time_in'   => $now->format('h:i:s A'),
                'date'      => $now->format('F d, Y'),
                'status'    => $status,
                'avatar'    => $user->profile_picture ? asset($user->profile_picture) : null,
            ]
        ], 200);
    }
}