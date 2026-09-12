@extends('layouts.app')

@section('title', 'NFC Card Binding - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70" 
     x-data="{ 
         searchQuery: '{{ request('student_name', '') }}', 
         selectedStudent: '{{ request('student_id', '') }}' 
     }">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                <i class="fa-solid fa-id-card-clip text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-slate-900 tracking-tight">NFC Card Binding</h1>
                <p class="text-[11px] text-slate-500 font-bold">Link physical NFC identification cards to student accounts</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 bg-amber-50 text-amber-900 text-[11px] font-black rounded-xl uppercase border border-amber-300 shadow-2xs flex items-center gap-2">
                <i class="fa-solid fa-wifi text-amber-700 animate-pulse"></i>
                <span>NFC Scanner Ready</span>
            </span>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="pt-6 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-6 flex-1 max-w-5xl mx-auto">

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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Side: Bind Card Form -->
            <div class="lg:col-span-7 bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xs font-black shrink-0">
                        <i class="fa-solid fa-link"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Assign NFC Tag to Student</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Search student and input or scan their card UID</p>
                    </div>
                </div>

                <form action="{{ route('admin.nfc.binding') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Searchable Student Selection -->
                    <div class="space-y-1.5" x-data="{ openDropdown: false }">
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Search Student (Name or LRN / ID)</label>
                        
                        <div class="relative">
                            <input type="text" x-model="searchQuery" @focus="openDropdown = true" placeholder="Type student name or ID number..." autocomplete="off"
                                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 pl-10 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                        </div>

                        <!-- Hidden select bound to form with Blade fallback -->
                        <select name="user_id" x-model="selectedStudent" required class="hidden">
                            <option value="">Select Student</option>
                            @foreach($users as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->last_name }}, {{ $student->first_name }} {{ $student->id_number }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Dynamic Search Results Dropdown -->
                        <div x-show="openDropdown && searchQuery.length > 0" @click.away="openDropdown = false" 
                             class="absolute z-30 mt-1 w-full bg-white border-2 border-slate-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="student in [
                                @foreach($users as $student)
                                    { id: '{{ $student->id }}', name: '{{ addslashes($student->last_name) }}, {{ addslashes($student->first_name) }}', lrn: '{{ $student->id_number ?? 'No ID' }}' },
                                @endforeach
                            ].filter(s => s.name.toLowerCase().includes(searchQuery.toLowerCase()) || s.lrn.toLowerCase().includes(searchQuery.toLowerCase()))">
                                <div @click="selectedStudent = student.id; searchQuery = student.name + ' (' + student.lrn + ')'; openDropdown = false;"
                                     class="px-4 py-3 hover:bg-amber-50 cursor-pointer transition flex items-center justify-between text-xs font-bold text-slate-800">
                                    <span x-text="student.name"></span>
                                    <span class="text-[10px] font-mono bg-slate-100 px-2 py-0.5 rounded-md text-slate-500" x-text="student.lrn"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- NFC Tag UID Input with Auto-Polling Support -->
                    <div class="space-y-1.5" x-data="{ 
                        tagId: '',
                        initPolling() {
                            setInterval(async () => {
                                try {
                                    let response = await fetch('/api/nfc/latest');
                                    let data = await response.json();
                                    if (data.card_uid && this.tagId !== data.card_uid) {
                                        this.tagId = data.card_uid;
                                    }
                                } catch (e) {
                                    // Ignore network errors during polling
                                }
                            }, 1000);
                        }
                    }" x-init="initPolling()">
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400">NFC Card UID / Tag ID</label>
                        <div class="relative">
                            <input type="text" 
                                   name="tag_id" 
                                   x-model="tagId"
                                   @keydown.enter.prevent=""
                                   placeholder="Tap card on hardware reader or type UID..." 
                                   required 
                                   autocomplete="off"
                                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 pl-10 text-xs font-bold font-mono text-slate-900 uppercase focus:outline-none focus:border-[#8b1818] transition">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-wifi text-xs"></i>
                            </div>
                        </div>
                        <p class="text-[9px] text-slate-400 font-bold">Tip: Tap your card on the hardware reader; the UID will automatically appear here!</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-down"></i> Save & Bind NFC Card
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Active Card Bindings -->
            <div class="lg:col-span-5 bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-4 flex flex-col">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-xs font-black shrink-0">
                        <i class="fa-solid fa-id-card text-amber-300"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Active Card Bindings</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Recently paired student ID cards</p>
                    </div>
                </div>

                <div class="space-y-2.5 overflow-y-auto max-h-[380px] pr-1 flex-1">
                    @forelse($boundCards as $card)
                    <div class="p-3.5 rounded-2xl bg-slate-50/70 border-2 border-slate-200 hover:border-amber-200 transition flex items-center justify-between gap-3">
                        <div class="space-y-0.5 overflow-hidden">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-900 rounded-md text-[10px] font-mono font-black border border-amber-200">
                                    {{ $card->tag_id }}
                                </span>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 truncate">
                                {{ $card->user->last_name ?? 'N/A' }}, {{ $card->user->first_name ?? '' }}
                            </h4>
                            <p class="text-[10px] text-slate-400 font-bold">
                                {{ $card->created_at ? $card->created_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>

                        <form action="{{ route('admin.nfc.destroy', $card->id) }}" method="POST" onsubmit="return confirm('Unbind this card?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 transition flex items-center justify-center text-xs border border-red-200 cursor-pointer" title="Unbind Card">
                                <i class="fa-solid fa-unlink"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="py-12 text-center text-slate-400 font-bold space-y-1 my-auto">
                        <div class="text-xl text-slate-300"><i class="fa-solid fa-id-card-clip"></i></div>
                        <p class="text-xs">No active NFC cards bound yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>
</div>
@endsection