@extends('layouts.app')

@section('title', 'Class Schedules Matrix - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-100/60">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 px-6 sm:px-10 lg:px-12 py-6 flex flex-col md:flex-row md:items-center justify-between gap-6 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#8b1818] text-white flex items-center justify-center text-lg shadow-sm shrink-0">
                <i class="fa-solid fa-calendar-days text-amber-300"></i>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Master Timetable Matrix</h1>
                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase tracking-wide shadow-2xs">A.Y. {{ $activeSchoolYear ?? '2026-2027' }}</span>
                </div>
                <p class="text-xs text-slate-500 font-bold mt-1">Time-slot grid allocation from Monday to Friday</p>
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-wider transition border-2 border-slate-200 shrink-0 cursor-pointer shadow-2xs">
                <i class="fa-solid fa-print text-sm text-[#8b1818]"></i>
                <span>Print</span>
            </button>

            <button type="button" onclick="openImportModal()"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-wider transition border-2 border-slate-200 shrink-0 cursor-pointer shadow-2xs">
                <i class="fa-solid fa-file-arrow-up text-sm text-blue-600"></i>
                <span>Import CSV</span>
            </button>

            <button type="button" 
                    onclick="openScheduleModal()"
                    class="inline-flex items-center gap-2.5 px-6 py-3 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-md shadow-red-950/20 transition-all duration-150 shrink-0 cursor-pointer">
                <i class="fa-solid fa-plus text-sm text-amber-300"></i>
                <span>Add Class Schedule</span>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="py-8 px-6 sm:px-10 lg:px-12 w-full space-y-8 flex-1 max-w-[1700px] mx-auto">

        @php
            $dbSections = \Illuminate\Support\Facades\Schema::hasTable('academic_sections') 
                ? \Illuminate\Support\Facades\DB::table('academic_sections')->orderBy('section_name')->get() 
                : collect();

            $dbStrands = \Illuminate\Support\Facades\Schema::hasTable('academic_sections') 
                ? \Illuminate\Support\Facades\DB::table('academic_sections')->whereNotNull('strand')->where('strand', '!=', '')->distinct()->pluck('strand') 
                : collect();

            $dbGrades = \Illuminate\Support\Facades\Schema::hasTable('academic_sections') 
                ? \Illuminate\Support\Facades\DB::table('academic_sections')->whereNotNull('grade_level')->where('grade_level', '!=', '')->distinct()->orderBy('grade_level')->pluck('grade_level') 
                : collect();

            // Mga oras para sa kaliwang kolum (Time Slots)
            $timeSlots = [
                '07:00 AM - 08:00 AM',
                '08:00 AM - 09:00 AM',
                '09:00 AM - 10:00 AM',
                '10:00 AM - 11:00 AM',
                '11:00 AM - 12:00 PM',
                '12:00 PM - 01:00 PM',
                '01:00 PM - 02:00 PM',
                '02:00 PM - 03:00 PM',
                '03:00 PM - 04:00 PM',
                '04:00 PM - 05:00 PM',
            ];

            $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        @endphp

        <!-- TOP CONTROLS: FILTERS & SEARCH CARD -->
        <div class="bg-white p-6 rounded-3xl border-2 border-slate-200 shadow-xs w-full">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between w-full">
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <select name="grade_level" onchange="this.form.submit()" class="py-3 px-4 text-xs font-bold rounded-2xl border-2 border-slate-200 bg-slate-50/50 text-slate-700 focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                        <option value="">All Grade Levels</option>
                        @foreach($dbGrades as $gradeItem)
                            <option value="{{ $gradeItem }}" {{ request('grade_level') == $gradeItem ? 'selected' : '' }}>{{ $gradeItem }}</option>
                        @endforeach
                    </select>

                    <select name="strand" onchange="this.form.submit()" class="py-3 px-4 text-xs font-bold rounded-2xl border-2 border-slate-200 bg-slate-50/50 text-slate-700 focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                        <option value="">All Strands</option>
                        @foreach($dbStrands as $strandItem)
                            <option value="{{ $strandItem }}" {{ request('strand') == $strandItem ? 'selected' : '' }}>{{ $strandItem }}</option>
                        @endforeach
                    </select>

                    <select name="section" onchange="this.form.submit()" class="py-3 px-4 text-xs font-bold rounded-2xl border-2 border-slate-200 bg-slate-50/50 text-slate-700 focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                        <option value="">All Sections</option>
                        @foreach($dbSections as $sec)
                            <option value="{{ $sec->section_name }}" {{ request('section') == $sec->section_name ? 'selected' : '' }}>{{ $sec->section_name }}</option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['grade_level', 'strand', 'section', 'day', 'search']))
                        <a href="{{ route('admin.schedules.index') }}" class="px-4 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl text-xs font-black transition shrink-0 shadow-2xs inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Clear Filters</span>
                        </a>
                    @endif
                </div>

                <div class="relative w-full lg:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subject or teacher..." 
                           class="w-full pl-10 pr-4 py-3 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-slate-50/50 transition shadow-2xs">
                </div>
            </form>
        </div>

        <!-- TIME-SLOT MATRIX TABLE CONTAINER -->
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto w-full">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-[#8b1818] text-white text-xs font-black uppercase tracking-wider text-center divide-x divide-red-950/40">
                            <th class="py-4 px-4 w-36 bg-[#731414]">Time Slot</th>
                            @foreach($weekdays as $day)
                                <th class="py-4 px-4">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/30">
                        @foreach($timeSlots as $slot)
                            @php
                                // Hatiin ang slot para sa matching (hal. "08:00 AM" at "09:00 AM")
                                list($slotStart, $slotEnd) = explode(' - ', $slot);
                            @endphp
                            <tr class="divide-x divide-slate-200">
                                <!-- TIME COLUMN (Kaliwa) -->
                                <td class="py-4 px-3 text-center font-mono font-black text-slate-600 bg-slate-100/80 whitespace-nowrap">
                                    {{ $slot }}
                                </td>

                                <!-- MONDAY TO FRIDAY COLUMNS -->
                                @foreach($weekdays as $day)
                                    @php
                                        // Hanapin ang schedule na tumutugma sa araw at time slot
                                        $matchedSchedule = $schedules->first(function($sched) use ($day, $slotStart) {
                                            $schedDay = trim($sched->day ?? $sched->day_of_week ?? '');
                                            if (strcasecmp($schedDay, $day) !== 0) return false;
                                            
                                            // I-format ang start time ng schedule para mag-match
                                            $sTime = !empty($sched->start_time) ? date('h:i A', strtotime($sched->start_time)) : '';
                                            return stripos($slotStart, trim($sTime)) !== false || trim($sTime) === trim(date('h:i A', strtotime($slotStart)));
                                        });
                                    @endphp

                                    <td class="p-2.5 align-top h-28 w-1/5 bg-white/50 relative">
                                        @if($matchedSchedule)
                                            <!-- Subject Card inside Grid Cell -->
                                            <div class="relative bg-white p-3 rounded-2xl border-2 border-slate-200 shadow-xs hover:shadow-md hover:border-[#8b1818] transition-all group overflow-hidden flex flex-col justify-between h-full">
                                                <!-- Red accent left border -->
                                                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#8b1818]"></div>

                                                <div class="space-y-1 pl-2">
                                                    <div class="flex items-center justify-between gap-1">
                                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[9px] font-black uppercase border border-slate-200">
                                                            {{ $matchedSchedule->grade_level ?? 'Gr' }} - {{ $matchedSchedule->section ?? 'Sec' }}
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded bg-red-50 text-[#8b1818] text-[9px] font-black uppercase border border-red-200">
                                                            {{ $matchedSchedule->strand ?? 'GEN' }}
                                                        </span>
                                                    </div>

                                                    <h4 class="text-xs font-black text-slate-900 group-hover:text-[#8b1818] transition leading-tight">
                                                        {{ $matchedSchedule->subject_name ?? $matchedSchedule->subject ?? optional($matchedSchedule->subjectRecord)->name ?? 'Subject' }}
                                                    </h4>

                                                    @if($matchedSchedule->teacher)
                                                        <p class="text-[10px] text-slate-500 font-bold">
                                                            {{ $matchedSchedule->teacher->first_name }} {{ $matchedSchedule->teacher->last_name }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="pt-2 border-t border-slate-100 pl-2 flex items-center justify-between mt-2">
                                                    <span class="font-mono text-[10px] font-bold text-amber-700">
                                                        {{ $matchedSchedule->start_time }} - {{ $matchedSchedule->end_time }}
                                                    </span>

                                                    <div class="flex items-center gap-1">
                                                        <button type="button" onclick="openEditScheduleModal({{ $matchedSchedule }})" title="Edit"
                                                                class="w-6 h-6 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 transition border border-amber-200 cursor-pointer inline-flex items-center justify-center text-[10px]">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                        <form action="{{ route('admin.schedules.destroy', $matchedSchedule->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" title="Delete"
                                                                    class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-[#8b1818] transition border border-red-200 cursor-pointer inline-flex items-center justify-center text-[10px]">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Empty Slot State -->
                                            <div class="h-full w-full flex items-center justify-center text-slate-300 text-[10px] font-semibold italic select-none">
                                                -
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($schedules, 'links'))
            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200">
                {{ $schedules->appends(request()->query())->links() }}
            </div>
        @endif

    </main>
</div>

<!-- INCLUDE MODALS -->
@include('admin.schedules.create')
@include('admin.schedules.import')

<!-- ================= Modal: Edit Class Schedule ================= -->
<div id="editScheduleModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 sm:p-6 transition-all duration-300 opacity-0 scale-95">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden flex flex-col max-h-[92vh] transform transition-all duration-300">
        <div class="px-8 py-6 border-b-2 border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900">Edit Class Schedule</h3>
            <button type="button" onclick="closeEditScheduleModal()" class="w-9 h-9 rounded-xl hover:bg-slate-200 text-slate-500 font-bold cursor-pointer">✕</button>
        </div>
        <form id="editScheduleForm" method="POST" class="p-8 overflow-y-auto space-y-6">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Subject Name *</label>
                <input type="text" id="edit_subject_name" name="subject_name" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Assigned Faculty *</label>
                <select id="edit_teacher_id" name="teacher_id" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                    @foreach($teachers as $t) <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option> @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Grade *</label>
                    <select id="edit_grade_level" name="grade_level" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        @foreach($dbGrades as $g) <option value="{{ $g }}">{{ $g }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Strand *</label>
                    <select id="edit_strand" name="strand" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        @foreach($dbStrands as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Section *</label>
                    <select id="edit_section" name="section" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        @foreach($dbSections as $sec) <option value="{{ $sec->section_name }}">{{ $sec->section_name }}</option> @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Day *</label>
                    <select id="edit_day" name="day" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Start Time *</label>
                    <input type="time" id="edit_start_time" name="start_time" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">End Time *</label>
                    <input type="time" id="edit_end_time" name="end_time" required class="w-full py-3 px-4 text-sm font-semibold rounded-2xl border-2 border-slate-200 focus:border-[#8b1818] outline-none bg-white">
                </div>
            </div>
            <div class="pt-6 border-t-2 border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeEditScheduleModal()" class="px-6 py-3 rounded-2xl border-2 border-slate-300 text-xs font-bold hover:bg-slate-50 cursor-pointer">Cancel</button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-[#8b1818] text-white text-xs font-black hover:bg-[#731414] cursor-pointer shadow-md">Update Schedule</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= Modal: Success Alert ================= -->
<div id="successModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm {{ session('success') ? 'flex' : 'hidden' }} items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 text-center space-y-6 shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto border border-emerald-200">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900">Success!</h3>
            <p class="text-xs font-bold text-slate-500 mt-1">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="closeSuccessModal()" class="w-full py-3 rounded-2xl bg-[#8b1818] text-white text-xs font-black uppercase cursor-pointer">Okay, Continue</button>
    </div>
</div>

<!-- ================= Modal: Error / Conflict Alert ================= -->
<div id="errorModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm {{ $errors->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white w-full max-w-sm rounded-3xl p-8 text-center space-y-6 shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-red-50 text-[#8b1818] flex items-center justify-center text-2xl mx-auto border border-red-200">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900">
                {{ str_contains($errors->first(), 'Conflict') ? 'Scheduling Conflict' : 'Validation Error' }}
            </h3>
            <p class="text-xs font-bold text-slate-500 mt-1.5 leading-relaxed">{{ $errors->first() }}</p>
        </div>
        <button type="button" onclick="closeErrorModal()" class="w-full py-3 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white text-xs font-black uppercase tracking-wider cursor-pointer transition">Got it, Thanks</button>
    </div>
</div>

@push('scripts')
<script>
    function openScheduleModal() {
        const modal = document.getElementById('scheduleModal');
        if (modal) { 
            modal.classList.remove('hidden'); 
            modal.classList.add('flex'); 
            setTimeout(() => { modal.classList.remove('opacity-0', 'scale-95'); modal.classList.add('opacity-100', 'scale-100'); }, 10);
            document.body.classList.add('overflow-hidden'); 
        }
    }
    function closeScheduleModal() {
        const modal = document.getElementById('scheduleModal');
        if (modal) { 
            modal.classList.remove('opacity-100', 'scale-100'); modal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); }, 200);
        }
    }
    function openEditScheduleModal(schedule) {
        const modal = document.getElementById('editScheduleModal');
        const form = document.getElementById('editScheduleForm');
        form.action = `/admin/schedules/${schedule.id}`;
        document.getElementById('edit_subject_name').value = schedule.subject_name || schedule.subject || '';
        document.getElementById('edit_teacher_id').value = schedule.teacher_id || '';
        document.getElementById('edit_grade_level').value = schedule.grade_level || schedule.academic_section?.grade_level || '';
        document.getElementById('edit_strand').value = schedule.strand || schedule.academic_section?.strand || '';
        document.getElementById('edit_section').value = schedule.section || schedule.academic_section?.section_name || '';
        document.getElementById('edit_day').value = schedule.day || schedule.day_of_week || 'Monday';
        document.getElementById('edit_start_time').value = schedule.start_time || '';
        document.getElementById('edit_end_time').value = schedule.end_time || '';
        if (modal) { 
            modal.classList.add('flex'); 
            setTimeout(() => { modal.classList.remove('opacity-0', 'scale-95'); modal.classList.add('opacity-100', 'scale-100'); }, 10);
            document.body.classList.add('overflow-hidden'); 
        }
    }
    function closeEditScheduleModal() {
        const modal = document.getElementById('editScheduleModal');
        if (modal) { 
            modal.classList.remove('opacity-100', 'scale-100'); modal.classList.add('opacity-0', 'scale-95');
            setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); }, 200);
        }
    }
    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) { modal.classList.remove('flex'); modal.classList.add('hidden'); }
    }
    function closeErrorModal() {
        const modal = document.getElementById('errorModal');
        if (modal) { modal.classList.remove('flex'); modal.classList.add('hidden'); }
    }
    function handleOutsideClick(event) {
        if (event.target === document.getElementById('scheduleModal')) closeScheduleModal();
        if (event.target === document.getElementById('editScheduleModal')) closeEditScheduleModal();
        if (event.target === document.getElementById('successModal')) closeSuccessModal();
        if (event.target === document.getElementById('errorModal')) closeErrorModal();
        if (event.target === document.getElementById('importScheduleModal')) closeImportModal();
    }
    window.addEventListener('click', handleOutsideClick);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeScheduleModal(); closeEditScheduleModal(); closeSuccessModal(); closeErrorModal(); closeImportModal(); }
    });
</script>
@endpush
@endsection