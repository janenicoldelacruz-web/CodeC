<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NfcCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminNfcController extends Controller
{
    // Ipakita ang Binding Page kasama ang mga users at naka-bind na cards
    public function bindingIndex()
    {
        $users = Schema::hasTable('users') ? User::orderBy('last_name')->get() : collect();
        $boundCards = Schema::hasTable('nfc_cards') ? NfcCard::with('user')->latest()->get() : collect();

        return view('admin.nfc.binding', compact('users', 'boundCards'));
    }

    // I-save o i-bind ang NFC card sa user
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

        return back()->with('success', 'NFC card successfully bound to user!');
    }

    // I-unbind o tanggalin ang NFC card
    public function bindingDestroy($id)
    {
        if (Schema::hasTable('nfc_cards')) {
            NfcCard::where('id', $id)->delete();
        }

        return back()->with('success', 'NFC card successfully unbound.');
    }
}