@extends('layouts.app')

@section('title', 'NFC Card Replacement - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                <i class="fa-solid fa-id-card-clip text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-slate-900 tracking-tight">NFC Card Replacement</h1>
                <p class="text-[11px] text-slate-500 font-bold">Replace lost or damaged NFC identification cards for students</p>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="pt-6 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-6 flex-1">

        @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            <span>Please complete all required fields correctly.</span>
        </div>
        @endif

        <!-- Replacement Form Card -->
        <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 shadow-xs space-y-4 max-w-2xl">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-xs border border-amber-200 shrink-0">
                    <i class="fa-solid fa-rotate"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Issue New NFC Card</h3>
                    <p class="text-[10px] text-slate-500 font-bold">Select a student and register their replacement card UID</p>
                </div>
            </div>

            <form action="{{ route('admin.nfc.replacement') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Select Student -->
                    <div class="sm:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">Select Student</label>
                        <select name="user_id" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                            <option value="" disabled selected>-- Choose Student Account --</option>
                            @foreach($users as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->last_name }}, {{ $student->first_name }} 
                                    @if($student->nfcCard) 
                                        (Current Card: {{ $student->nfcCard->tag_id }})
                                    @else 
                                        (No Card Yet)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New NFC Tag UID Input -->
                    <div class="sm:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">New NFC Card UID / Tag ID</label>
                        <input type="text" name="new_tag_id" placeholder="Tap replacement card on reader or type UID..." required autocomplete="off"
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold font-mono text-slate-900 uppercase focus:outline-none focus:border-[#8b1818] transition">
                    </div>

                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> Update / Replace NFC Card
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection