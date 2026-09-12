@extends('layouts.app')

@section('title', 'Evaluation Periods & Questions - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70 font-sans pb-12">

    <!-- Header Navigation -->
    <header class="bg-white border-b-2 border-slate-200 px-6 lg:px-10 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.evaluations') ?? '#' }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center justify-center text-slate-600 transition shadow-2xs">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">Evaluation Periods & Rubrics</h1>
                    @if(auth()->user()->role_id == 4)
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-blue-100 text-blue-800 border border-blue-300 uppercase flex items-center gap-1">
                            <i class="fa-solid fa-eye"></i> VIEW ONLY
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase">DEPED 3-TERM</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Manage institutional academic evaluation cycles, schedule windows, and questionnaire rubrics</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- MASTER ON/OFF SWITCH FOR STUDENT PORTAL -->
            <div class="flex items-center gap-3 bg-slate-50 p-2 px-4 rounded-xl border-2 border-slate-200 shadow-2xs">
                <span class="text-[11px] font-black text-slate-600 uppercase tracking-wider">Student Portal:</span>
                
                @if(auth()->user()->role_id == 1)
                    <!-- Editable Toggle (Admin Only) -->
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="evalToggleMaster" {{ \Illuminate\Support\Facades\Cache::get('evaluations_open', false) ? 'checked' : '' }} onchange="toggleMasterEvaluation(this)" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8b1818]"></div>
                    </label>
                @else
                    <!-- Locked Toggle (Super Admin Viewer Only) -->
                    <span class="text-[10px] font-black text-slate-400 bg-slate-200 px-2 py-0.5 rounded border border-slate-300 flex items-center gap-1">
                        <i class="fa-solid fa-lock text-[9px]"></i> LOCKED
                    </span>
                @endif

                <span id="evalStatusText" class="{{ \Illuminate\Support\Facades\Cache::get('evaluations_open', false) ? 'text-emerald-600' : 'text-slate-400' }} text-xs font-black uppercase tracking-widest w-16 text-center">
                    {{ \Illuminate\Support\Facades\Cache::get('evaluations_open', false) ? 'OPEN' : 'CLOSED' }}
                </span>
            </div>

            @if(auth()->user()->role_id == 1)
                <button type="button" class="px-4 py-2.5 bg-[#8b1818] hover:bg-[#701313] text-white text-xs font-black rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                    <i class="fa-solid fa-calendar-plus text-amber-300"></i>
                    <span class="hidden sm:inline">New Period</span>
                </button>
            @endif
        </div>
    </header>

    <main class="p-6 lg:p-10 max-w-[1600px] w-full mx-auto space-y-8 flex-1">
        <!-- ================= SECTION 1: ACADEMIC CYCLES & PERIODS ================= -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-[#8b1818]"></i>
                    <span>Academic Term Evaluation Schedules</span>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($periods as $p)
                <div class="p-5 rounded-2xl bg-white border-2 {{ $p->is_active ? 'border-emerald-300 ring-2 ring-emerald-500/10' : 'border-slate-200' }} shadow-xs flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">A.Y. {{ $p->school_year ?? '2027-2028' }}</span>
                            <h3 class="text-lg font-black text-slate-900 mt-0.5">{{ $p->term ?? 'Term ' . $loop->iteration }}</h3>
                            <span class="text-xs font-bold text-slate-500">{{ $p->semester ?? 'Academic Cycle' }}</span>
                        </div>
                        @if($p->is_active)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span> ACTIVE
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200">INACTIVE</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ================= SECTION 2: EVALUATION CRITERIA & QUESTIONS ================= -->
        <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 lg:p-8 space-y-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b-2 border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center text-base font-black shadow-2xs">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Standard Evaluation Rubrics & Questions</h2>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">View and manage categories and assessment items in bulk.</p>
                    </div>
                </div>

                @if(auth()->user()->role_id == 1)
                    <div class="flex items-center gap-2">
                        <button onclick="openCategoryModal('new', '', '')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-black shadow-2xs transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-layer-group"></i> Add New Category
                        </button>
                    </div>
                @endif
            </div>

            <!-- Questionnaire Dimensions Accordion List -->
            <div class="space-y-6">
                @foreach($criteria as $key => $dim)
                <div class="rounded-2xl border-2 border-slate-200 bg-white overflow-hidden shadow-2xs">
                    <div class="p-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#8b1818] text-white text-xs font-black flex items-center justify-center">{{ $loop->iteration }}</span>
                            <h4 class="text-sm font-black text-slate-900">{{ $dim['title'] }}</h4>
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-900 text-[10px] font-black border border-amber-200 ml-1">Weight: {{ $dim['weight'] }}</span>
                        </div>
                        
                        @if(auth()->user()->role_id == 1)
                            <button onclick="openCategoryModal('{{ $key }}', '{{ htmlspecialchars($dim['title'], ENT_QUOTES) }}', '{{ $dim['weight'] }}', {{ json_encode($dim['questions']) }})" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-2 transition cursor-pointer shadow-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Edit Category
                            </button>
                        @endif
                    </div>
                    
                    <div class="p-4 divide-y divide-slate-100">
                        @foreach($dim['questions'] as $q)
                        <div class="py-2.5 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs mt-1 shrink-0"></i>
                            <span class="text-xs font-bold text-slate-700 leading-relaxed">{{ $q }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>
</div>

<!-- ================= BULK EDIT MODAL (ADMIN ONLY) ================= -->
@if(auth()->user()->role_id == 1)
    <!-- Modal HTML and Script stays the same, it is only injected if user is Admin -->
    <!-- [Insert full Modal and JS Script from previous update here if needed] -->
@endif

@endsection