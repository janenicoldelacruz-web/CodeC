<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNfcController extends Controller
{
    public function binding()
    {
        return view('admin.nfc.binding');
    }

    public function replacement()
    {
        return view('admin.nfc.replacement');
    }
}