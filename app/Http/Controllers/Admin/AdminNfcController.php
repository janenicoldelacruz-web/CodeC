<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NfcCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class AdminNfcController extends Controller
{
    // Ipakita ang Binding Page (Students Only - Role ID 3)
    public function bindingIndex()
    {
        $users = Schema::hasTable('users') 
            ? User::where('role_id', 3)->orderBy('last_name')->get() 
            : collect();

        $boundCards = Schema::hasTable('nfc_cards') 
            ? NfcCard::with('user')->latest()->get() 
            : collect();

        return view('admin.nfc.binding', compact('users', 'boundCards'));
    }

    // I-save o i-bind ang NFC card sa student
    public function bindingStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tag_id' => 'required|string|max:255',
        ]);

        $tagId = strtoupper(trim($request->tag_id));

        // 1. Suriin kung may ibang estudyante na gumagamit ng card na ito
        $existingCard = NfcCard::where('tag_id', $tagId)
            ->where('user_id', '!=', $request->user_id)
            ->with('user')
            ->first();

        if ($existingCard) {
            $studentName = $existingCard->user 
                ? "{$existingCard->user->last_name}, {$existingCard->user->first_name}" 
                : "another student";

            return redirect()->route('admin.nfc.binding')
                ->with('duplicate_error', "Ang NFC Card na may UID [{$tagId}] ay nakakabit na kay {$studentName}!");
        }

        // 2. I-save kapag walang duplicate
        if (Schema::hasTable('nfc_cards')) {
            NfcCard::updateOrCreate(
                ['user_id' => $request->user_id],
                ['tag_id' => $tagId, 'status' => 'active']
            );

            Cache::forget('latest_nfc_tap');
        }

        return redirect()->route('admin.nfc.binding')->with('success', 'NFC card successfully bound to student!');
    }

    // I-unbind ang NFC card (Lost o Replacement - Parehong magbubura / unbind)
    public function bindingDestroy(Request $request, $id)
    {
        if (Schema::hasTable('nfc_cards')) {
            $nfcCard = NfcCard::with('user')->find($id);
            
            if ($nfcCard) {
                $reason = $request->input('unbind_reason', 'replacement');
                $userId = $nfcCard->user_id;
                $student = $nfcCard->user;
                $studentName = $student ? "{$student->first_name} {$student->last_name}" : "Student";
                $tagId = $nfcCard->tag_id;

                if ($reason === 'lost') {
                    // KUNG LOST: Direktang burahin / unbind ang card
                    $nfcCard->delete();

                    return back()->with('success', "Card UID [{$tagId}] for {$studentName} was reported LOST and successfully unlinked.");
                } 
                
                if ($reason === 'replacement') {
                    // KUNG REPLACEMENT: Burahin ang card at i-redirect sa binding page para sa bagong scan
                    $nfcCard->delete();

                    $encodedName = urlencode(($student->last_name ?? '') . ', ' . ($student->first_name ?? '') . ' (' . ($student->id_number ?? 'No ID') . ')');

                    return redirect()->route('admin.nfc.binding', [
                        'student_id' => $userId,
                        'student_name' => $encodedName
                    ])->with('success', "Previous card unlinked for replacement. Please scan the new card for {$studentName}.");
                }
            }
        }

        return back()->with('error', 'NFC card record not found.');
    }

    // API: Tanggapin ang NFC tap mula sa Python Bridge at i-cache ito nang pansamantala
    public function handleTap(Request $request)
    {
        $uid = $request->input('card_uid') ?? $request->input('tag_id');
        
        if ($uid) {
            Cache::put('latest_nfc_tap', strtoupper(trim($uid)), 30);
        }

        return response()->json([
            'success' => true,
            'message' => 'Card tap received successfully',
            'uid' => $uid
        ]);
    }

    // API: Kunin ang pinakahuling na-tap na card para sa browser auto-fill polling
    public function getLatestTap()
    {
        $uid = Cache::get('latest_nfc_tap', '');
        return response()->json(['card_uid' => $uid]);
    }
}