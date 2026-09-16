@extends('layouts.app')

@section('title', 'Evaluation Periods & Rubrics - SIATRACK Admin')

@section('content')
<div x-data="{
        periodModal: false,
        addQuestionModal: false,
        editQuestionModal: false,
        currentQuestion: { id: null, category: '', question: '' },
        openEdit(q) {
            this.currentQuestion = { id: q.id, category: q.category, question: q.question };
            this.editQuestionModal = true;
        }
    }" 
    class="w-full min-h-screen bg-slate-100/60 pb-20 font-sans">
    
    <!-- Top Bar Navigation with Clean Elevation -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-6 sm:px-10 py-4 sticky top-0 z-30 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#8b1818] to-[#590d0d] text-white flex items-center justify-center text-lg shadow-md shadow-red-950/20 shrink-0">
                <i class="fa-solid fa-clipboard-check text-amber-300"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">SIA Faculty Appraisal Rubrics</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300/60">SY 2025-2026</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold">Configure active academic periods, rubrics, and evaluation indicators</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button @click="addQuestionModal = true" class="px-4 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider transition shadow-md shadow-red-950/20 flex items-center gap-2">
                <i class="fa-solid fa-plus text-amber-300 text-xs"></i>
                <span>Add Indicator</span>
            </button>
            <a href="{{ url('admin/evaluations/results') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-wider transition border border-slate-300 shadow-2xs flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-slate-500 text-xs"></i>
                <span>View Analytics</span>
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto pt-6 px-4 sm:px-6 lg:px-8 space-y-6">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-xs font-bold rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <!-- Cycle Status Banner (Fixed Margin, Clean Grid) -->
        <div class="bg-gradient-to-r from-[#590d0d] via-[#731414] to-[#8b1818] rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-red-950/15 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-white/10 gap-4">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-300">Active Appraisal Period</span>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight mt-0.5">{{ $activePeriod->semester ?? '1st Semester' }} &bull; {{ $activePeriod->school_year ?? 'SY 2025-2026' }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="periodModal = true" class="px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white backdrop-blur-md border border-white/20 text-xs font-black transition flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-amber-300"></i>
                        <span>Modify Cycle Parameters</span>
                    </button>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-black/20 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                    <p class="text-[10px] font-black uppercase tracking-wider text-red-200/70">Semester Term</p>
                    <h4 class="text-base font-extrabold text-white mt-1">{{ $activePeriod->semester ?? '1st Semester' }}</h4>
                    <span class="text-[11px] font-semibold text-amber-300">Regular Academic Cycle</span>
                </div>
                <div class="bg-black/20 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                    <p class="text-[10px] font-black uppercase tracking-wider text-red-200/70">Academic Year</p>
                    <h4 class="text-base font-extrabold text-white mt-1">{{ $activePeriod->school_year ?? 'SY 2025-2026' }}</h4>
                    <span class="text-[11px] font-semibold text-red-200">Institutional DepEd Calendar</span>
                </div>
                <div class="bg-black/20 backdrop-blur-sm rounded-2xl p-4 border border-white/10">
                    <p class="text-[10px] font-black uppercase tracking-wider text-red-200/70">Submission Window</p>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ ($activePeriod->status ?? 'open') === 'open' ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400' }}"></span>
                        <h4 class="text-base font-black uppercase text-white">{{ $activePeriod->status ?? 'OPEN' }}</h4>
                    </div>
                    <span class="text-[11px] font-semibold text-red-200">Live for student & peer feedback</span>
                </div>
            </div>
        </div>

        <!-- Sleek Segmented Form Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <div class="inline-flex p-1.5 bg-slate-200/80 rounded-2xl gap-1.5 border border-slate-300/70 w-full sm:w-auto overflow-x-auto">
                <a href="{{ url('admin/evaluations/periods?type=principal') }}" 
                   class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition whitespace-nowrap flex items-center gap-2 {{ $selectedType === 'principal' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40' }}">
                    <i class="fa-solid fa-user-tie text-xs"></i>
                    <span>Principal's Evaluation</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] {{ $selectedType === 'principal' ? 'bg-red-50 text-[#8b1818]' : 'bg-slate-300 text-slate-700' }}">{{ $counts['principal'] }}</span>
                </a>

                <a href="{{ url('admin/evaluations/periods?type=peer') }}" 
                   class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition whitespace-nowrap flex items-center gap-2 {{ $selectedType === 'peer' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40' }}">
                    <i class="fa-solid fa-users text-xs"></i>
                    <span>Peer Evaluation</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] {{ $selectedType === 'peer' ? 'bg-red-50 text-[#8b1818]' : 'bg-slate-300 text-slate-700' }}">{{ $counts['peer'] }}</span>
                </a>

                <a href="{{ url('admin/evaluations/periods?type=student') }}" 
                   class="px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition whitespace-nowrap flex items-center gap-2 {{ $selectedType === 'student' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40' }}">
                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                    <span>Student Evaluation</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] {{ $selectedType === 'student' ? 'bg-red-50 text-[#8b1818]' : 'bg-slate-300 text-slate-700' }}">{{ $counts['student'] }}</span>
                </a>
            </div>

            <!-- Reseed / Sync with Official SIA Document -->
            <form method="POST" action="{{ url('admin/evaluations/periods/reset') }}" onsubmit="return confirm('Restore official SIA items for this form? Customized edits will be replaced by the official questionnaire.');">
                @csrf
                <input type="hidden" name="form_type" value="{{ $selectedType }}">
                <button type="submit" class="text-xs font-bold text-slate-500 hover:text-[#8b1818] flex items-center gap-1.5 transition py-2 px-3 rounded-xl hover:bg-slate-200/60">
                    <i class="fa-solid fa-rotate text-[11px]"></i>
                    <span>Reload Official Rubric</span>
                </button>
            </form>
        </div>

        <!-- Categorized Indicators Accordion & Cards -->
        <div class="space-y-6">
            @forelse($groupedQuestions as $category => $items)
                <div class="bg-white border-2 border-slate-200/90 rounded-3xl overflow-hidden shadow-xs">
                    <!-- Category Header Bar -->
                    <div class="bg-slate-50/90 border-b-2 border-slate-200/90 px-6 sm:px-8 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-6 rounded-full bg-[#8b1818]"></span>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">{{ $category }}</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-white border border-slate-200 text-slate-600 shadow-2xs">
                            {{ count($items) }} {{ Str::plural('Indicator', count($items)) }}
                        </span>
                    </div>

                    <!-- Items List -->
                    <div class="divide-y divide-slate-100">
                        @foreach($items as $q)
                            <div class="p-5 sm:px-8 flex items-start justify-between gap-4 hover:bg-slate-50/60 transition group">
                                <div class="flex items-start gap-4 max-w-4xl">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 font-black text-xs flex items-center justify-center shrink-0 border border-slate-200 mt-0.5 group-hover:bg-red-50 group-hover:text-[#8b1818] group-hover:border-red-200 transition">
                                        {{ $q->order_num }}
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-bold text-slate-800 leading-relaxed">{{ $q->question }}</p>
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            <i class="fa-solid fa-check-double text-emerald-500"></i>
                                            Scale 1–5 (DepEd Rubric)
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0 pt-0.5">
                                    <button @click="openEdit({{ json_encode($q) }})" 
                                            class="p-2 rounded-xl text-slate-400 hover:text-amber-600 hover:bg-amber-50 border border-transparent hover:border-amber-200 transition" 
                                            title="Edit Indicator">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    
                                    <form method="POST" action="{{ url('admin/evaluations/questions/' . $q->id) }}" onsubmit="return confirm('Delete this indicator permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-200 transition" 
                                                title="Delete Indicator">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white border-2 border-dashed border-slate-300 rounded-3xl p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800">No Indicators Configured Yet</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Click "Reload Official Rubric" above or create customized criteria manually.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Modal: Edit Appraisal Cycle -->
    <div x-show="periodModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="periodModal = false" class="bg-white rounded-3xl border-2 border-slate-200 max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-4 border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Appraisal Parameters</h3>
                        <p class="text-xs text-slate-400 font-bold">Configure active cycle details</p>
                    </div>
                </div>
                <button @click="periodModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>
            
            <form method="POST" action="{{ url('admin/evaluations/periods/save') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Academic Semester</label>
                    <input type="text" name="semester" value="{{ $activePeriod->semester ?? '1st Semester' }}" required class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">School Year Range</label>
                    <input type="text" name="school_year" value="{{ $activePeriod->school_year ?? 'SY 2025-2026' }}" required class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Submission Window Status</label>
                    <select name="status" class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818] bg-white">
                        <option value="open" {{ ($activePeriod->status ?? 'open') === 'open' ? 'selected' : '' }}>OPEN (Allow Student & Peer Submissions)</option>
                        <option value="closed" {{ ($activePeriod->status ?? 'open') === 'closed' ? 'selected' : '' }}>CLOSED (Disable Submissions)</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="periodModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-black text-xs text-slate-600 uppercase">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#8b1818] font-black text-xs text-white uppercase shadow-md shadow-red-950/20">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Add Question -->
    <div x-show="addQuestionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="addQuestionModal = false" class="bg-white rounded-3xl border-2 border-slate-200 max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-4 border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-[#8b1818] flex items-center justify-center text-sm">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Add Rubric Indicator</h3>
                        <p class="text-xs text-slate-400 font-bold">Appending to {{ strtoupper($selectedType) }} form</p>
                    </div>
                </div>
                <button @click="addQuestionModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="{{ url('admin/evaluations/questions') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="form_type" value="{{ $selectedType }}">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Category / Domain Section</label>
                    <input type="text" name="category" placeholder="e.g. I. Instructional Competence (50% Weight)" required class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Question / Behavioral Indicator Statement</label>
                    <textarea name="question" rows="4" placeholder="Type evaluation indicator..." required class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818]"></textarea>
                </div>
                <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="addQuestionModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-black text-xs text-slate-600 uppercase">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#8b1818] font-black text-xs text-white uppercase shadow-md shadow-red-950/20">Add to Rubric</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Question -->
    <div x-show="editQuestionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editQuestionModal = false" class="bg-white rounded-3xl border-2 border-slate-200 max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="flex items-center justify-between border-b pb-4 border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Modify Indicator</h3>
                        <p class="text-xs text-slate-400 font-bold">Update questionnaire item details</p>
                    </div>
                </div>
                <button @click="editQuestionModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="'{{ url('admin/evaluations/questions') }}/' + currentQuestion.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Category / Domain Section</label>
                    <input type="text" name="category" x-model="currentQuestion.category" required class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Question / Indicator Statement</label>
                    <textarea name="question" rows="4" x-model="currentQuestion.question" required class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl outline-none focus:border-[#8b1818]"></textarea>
                </div>
                <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="editQuestionModal = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-black text-xs text-slate-600 uppercase">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#8b1818] font-black text-xs text-white uppercase shadow-md shadow-red-950/20">Update Indicator</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection