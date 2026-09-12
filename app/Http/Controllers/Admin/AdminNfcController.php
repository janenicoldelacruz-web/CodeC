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

        if (Schema::hasTable('nfc_cards')) {
            NfcCard::updateOrCreate(
                ['user_id' => $request->user_id],
                ['tag_id' => strtoupper(trim($request->tag_id))]
            );
        }

        return back()->with('success', 'NFC card successfully bound to student!');
    }

    // I-unbind o tanggalin ang NFC card
    public function bindingDestroy($id)
    {
        if (Schema::hasTable('nfc_cards')) {
            NfcCard::where('id', $id)->delete();
        }

        return back()->with('success', 'NFC card successfully unbound.');
    }

    // Ipakita ang Replacement Page (Students Only - Role ID 3)
    public function replacementIndex()
    {
        $users = Schema::hasTable('users') 
            ? User::with('nfcCard')->where('role_id', 3)->orderBy('last_name')->get() 
            : collect();

        return view('admin.nfc.replacement', compact('users'));
    }

    // I-proseso ang pagpapalit ng NFC Card para sa student
    public function replacementStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_tag_id' => 'required|string|max:255',
        ]);

        if (Schema::hasTable('nfc_cards')) {
            NfcCard::updateOrCreate(
                ['user_id' => $request->user_id],
                ['tag_id' => strtoupper(trim($request->new_tag_id))]
            );
        }

        return back()->with('success', 'NFC card successfully replaced and updated!');
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