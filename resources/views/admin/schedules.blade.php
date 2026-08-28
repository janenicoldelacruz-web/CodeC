@extends('layouts.app')

@section('title', 'Class Schedules - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-base shadow-xs shrink-0">
                <i class="fa-solid fa-calendar-days text-amber-300"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Class Schedules</h1>
                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase tracking-wide shadow-2xs">
                        A.Y. {{ date('Y') }}-{{ date('Y') + 1 }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Manage subject loads, section timetables, and faculty assignments</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" 
                    onclick="openScheduleModal()"
                    class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/20 hover:shadow-xl transition-all duration-150 active:scale-[0.98] border-b-4 border-[#5e0f0f] shrink-0 cursor-pointer">
                <i class="fa-solid fa-plus text-sm text-amber-300"></i>
                <span>Add Class Schedule</span>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="pt-12 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-8 flex-1">

        @if($errors->any())
            <div class="p-4 bg-red-50 border-2 border-red-300 text-red-800 text-xs font-bold rounded-2xl flex items-center gap-2.5 shadow-xs w-full">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-amber-300 transition w-full">
                <div>
                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Academic Sections</p>
                    <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalSections ?? 0) }}</h3>
                </div>
                <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                    <span>Active Section Count</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                </div>
            </div>

            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-blue-300 transition w-full">
                <div>
                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Curriculum Subjects</p>
                    <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalSubjects ?? 0) }}</h3>
                </div>
                <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                    <span>Assigned Courses</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                </div>
            </div>

            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-red-300 transition w-full">
                <div>
                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Teaching Staff</p>
                    <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($assignedFacultyCount ?? 0) }}</h3>
                </div>
                <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                    <span>Faculty with Active Load</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8b1818]"></span>
                </div>
            </div>
        </div>

        <!-- TOP CONTROLS: FILTERS & SEARCH -->
        <form method="GET" action="{{ route('admin.schedules') }}" class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between w-full">
            <div class="flex flex-wrap items-center gap-2.5">
                <select name="grade_level" onchange="this.form.submit()" class="py-2.5 px-3.5 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                    <option value="">All Grade Levels</option>
                    <option value="Grade 11" {{ request('grade_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                    <option value="Grade 12" {{ request('grade_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                </select>

                <select name="strand" onchange="this.form.submit()" class="py-2.5 px-3.5 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                    <option value="">All Tracks</option>
                    <option value="Academic Track" {{ request('strand') == 'Academic Track' ? 'selected' : '' }}>Academic Track</option>
                    <option value="Technical-Professional" {{ request('strand') == 'Technical-Professional' ? 'selected' : '' }}>Technical-Professional</option>
                </select>

                <select name="section" onchange="this.form.submit()" class="py-2.5 px-3.5 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                    <option value="">All Sections</option>
                    <option value="Amber" {{ request('section') == 'Amber' ? 'selected' : '' }}>Amber</option>
                    <option value="Crystal" {{ request('section') == 'Crystal' ? 'selected' : '' }}>Crystal</option>
                    <option value="Pearl" {{ request('section') == 'Pearl' ? 'selected' : '' }}>Pearl</option>
                    <option value="Turquoise" {{ request('section') == 'Turquoise' ? 'selected' : '' }}>Turquoise</option>
                </select>

                <select name="day" onchange="this.form.submit()" class="py-2.5 px-3.5 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-700 focus:bg-white focus:outline-none focus:border-[#8b1818] transition shadow-2xs cursor-pointer">
                    <option value="">All Weekdays</option>
                    <option value="Monday" {{ request('day') == 'Monday' ? 'selected' : '' }}>Monday</option>
                    <option value="Tuesday" {{ request('day') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                    <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                    <option value="Thursday" {{ request('day') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                    <option value="Friday" {{ request('day') == 'Friday' ? 'selected' : '' }}>Friday</option>
                    <option value="Mon / Wed" {{ request('day') == 'Mon / Wed' ? 'selected' : '' }}>Mon / Wed</option>
                    <option value="Tue / Thu" {{ request('day') == 'Tue / Thu' ? 'selected' : '' }}>Tue / Thu</option>
                </select>

                @if(request()->hasAny(['grade_level', 'strand', 'section', 'day', 'search']))
                    <a href="{{ route('admin.schedules') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-black transition shrink-0 shadow-2xs inline-flex items-center gap-1.5">
                        <span>Clear</span>
                    </a>
                @endif
            </div>

            <!-- Search Box -->
            <div class="relative w-full md:w-96">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subject or teacher..." 
                       class="w-full px-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-white transition shadow-2xs">
            </div>
        </form>

        <!-- SECTION: CLASS SCHEDULES DIRECTORY -->
        <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-6 w-full">
            <div class="flex items-center justify-between border-b-2 border-slate-100 pb-5">
                <div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">Active Class Schedules</h2>
                    <p class="text-xs text-slate-500 font-bold mt-0.5">Faculty course load timetable</p>
                </div>
                <div>
                    <span class="text-xs font-black text-[#8b1818] bg-red-50 px-3.5 py-1.5 rounded-full border border-red-200 shadow-2xs">
                        Total Schedules: {{ $schedules->total() }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border-2 border-slate-200 shadow-2xs w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-5">Subject & Code</th>
                            <th class="py-4 px-5">Assigned Faculty</th>
                            <th class="py-4 px-5 text-center">Grade Level</th>
                            <th class="py-4 px-5 text-center">Track</th>
                            <th class="py-4 px-5 text-center">Section</th>
                            <th class="py-4 px-5 text-center">Day</th>
                            <th class="py-4 px-5 text-center">Time Slot</th>
                            <th class="py-4 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
    @forelse($schedules as $schedule)
        <tr class="hover:bg-red-50/40 transition">
            <td class="py-4 px-5">
                <div class="font-extrabold text-slate-900 text-sm leading-tight">
                    {{ $schedule->subject_name ?? $schedule->subject ?? optional($schedule->subjectRecord)->name ?? 'Unnamed Subject' }}
                </div>
                <span class="block text-xs font-mono text-slate-400 uppercase font-bold tracking-wider mt-0.5">
                    {{ $schedule->subject_code ?? optional($schedule->subjectRecord)->code ?? 'NO-CODE' }}
                </span>
            </td>

            <td class="py-4 px-5">
                @if($schedule->teacher)
                    <div class="font-extrabold text-slate-900 text-sm leading-tight">
                        {{ $schedule->teacher->first_name }} {{ $schedule->teacher->last_name }}
                    </div>
                    <span class="block text-xs text-slate-400 font-medium">{{ $schedule->teacher->email }}</span>
                @else
                    <span class="text-slate-400 italic text-xs font-medium">Unassigned</span>
                @endif
            </td>

            <td class="py-4 px-5 text-center">
                <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-100 text-slate-800 border border-slate-300 shadow-2xs">
                    {{ $schedule->grade_level ?? 'Grade 11' }}
                </span>
            </td>

            <td class="py-4 px-5 text-center">
                <span class="px-3 py-1 rounded-xl text-xs font-black bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                    {{ $schedule->strand ?: 'Academic Track' }}
                </span>
            </td>

            <td class="py-4 px-5 text-center">
                <span class="px-3 py-1 rounded-xl bg-red-50 text-[#8b1818] text-xs font-black border border-red-200 shadow-2xs">
                    {{ $schedule->section ?? 'Amber' }}
                </span>
            </td>

            <td class="py-4 px-5 text-center text-slate-700 font-bold">
                {{ $schedule->day ?? $schedule->day_of_week ?? 'Monday' }}
            </td>

            <td class="py-4 px-5 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-mono text-xs font-bold">
                    {{ $schedule->time_slot ?? ($schedule->start_time . ' - ' . $schedule->end_time) }}
                </span>
            </td>

            <td class="py-4 px-5 text-right space-x-1.5 whitespace-nowrap">
                <!-- Edit Schedule Icon Button -->
                <button type="button" onclick="openEditScheduleModal({{ $schedule }})" title="Edit Schedule Details"
                        class="w-9 h-9 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 font-extrabold text-xs transition border border-amber-200 shadow-2xs cursor-pointer inline-flex items-center justify-center">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                </button>

                <!-- Delete Schedule Form Icon Button -->
                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this class schedule?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Delete Schedule"
                            class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] font-extrabold text-xs transition border border-red-200 shadow-2xs cursor-pointer inline-flex items-center justify-center">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="py-16 text-center text-slate-400 font-medium">
                <p class="text-base font-extrabold text-slate-800">No Class Schedules Found</p>
                <p class="text-xs text-slate-500 font-semibold mt-1">Try adjusting your filters or create a new schedule entry.</p>
            </td>
        </tr>
    @endforelse
</tbody>
                </table>
            </div>

            @if(method_exists($schedules, 'links'))
                <div class="pt-2">
                    {{ $schedules->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </main>
</div>

<!-- ================= Modal: Add Class Schedule ================= -->
<div id="scheduleModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 sm:p-6 transition-opacity duration-200" onclick="handleOutsideClick(event)">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden transform transition-all flex flex-col max-h-[92vh]">
        
        <div class="px-8 py-5 border-b-2 border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Create Class Schedule</h3>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Assign faculty loads, tracks, sections, and time slots</p>
            </div>
            <button type="button" onclick="closeScheduleModal()" class="w-9 h-9 rounded-xl hover:bg-slate-200/70 text-slate-400 hover:text-slate-700 flex items-center justify-center transition focus:outline-none cursor-pointer">
                ✕
            </button>
        </div>

        <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-8 overflow-y-auto space-y-6">
            @csrf

            <!-- Subject Details -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Subject Name <span class="text-red-600">*</span></label>
                        <input type="text" name="subject_name" required 
                               class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Subject Code</label>
                        <input type="text" name="subject_code" 
                               class="w-full py-2.5 px-3 text-sm font-mono font-bold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none uppercase bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Assigned Faculty Instructor <span class="text-red-600">*</span></label>
                    <select name="teacher_id" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="">Select Teaching Faculty</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }} ({{ $teacher->id_number ?? $teacher->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grade, Track & Section -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Grade Level <span class="text-red-600">*</span></label>
                        <select name="grade_level" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                            <option value="">Select Grade Level</option>
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Track <span class="text-red-600">*</span></label>
                        <select name="strand" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                            <option value="">Select Track</option>
                            <option value="Academic Track">Academic Track</option>
                            <option value="Technical-Professional">Technical-Professional</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Section <span class="text-red-600">*</span></label>
                        <select name="section" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                            <option value="">Select Section</option>
                            <option value="Amber">Amber</option>
                            <option value="Crystal">Crystal</option>
                            <option value="Pearl">Pearl</option>
                            <option value="Turquoise">Turquoise</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Day & Time Allocation -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Day Schedule <span class="text-red-600">*</span></label>
                    <select name="day" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Mon / Wed">Mon / Wed</option>
                        <option value="Tue / Thu">Tue / Thu</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Start Time <span class="text-red-600">*</span></label>
                        <input type="time" name="start_time" required class="w-full py-2.5 px-3.5 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">End Time <span class="text-red-600">*</span></label>
                        <input type="time" name="end_time" required class="w-full py-2.5 px-3.5 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-5 border-t-2 border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeScheduleModal()" class="px-5 py-2.5 rounded-xl border-2 border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-extrabold transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#721313] text-white text-xs font-black shadow-md shadow-red-950/20 hover:shadow-lg transition cursor-pointer">
                    Save Schedule
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ================= Modal: Edit Class Schedule ================= -->
<div id="editScheduleModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 sm:p-6 transition-opacity duration-200">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden transform transition-all flex flex-col max-h-[92vh]">
        
        <div class="px-8 py-5 border-b-2 border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Class Schedule</h3>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Modify faculty loads, tracks, sections, and time slots</p>
            </div>
            <button type="button" onclick="closeEditScheduleModal()" class="w-9 h-9 rounded-xl hover:bg-slate-200/70 text-slate-400 hover:text-slate-700 flex items-center justify-center transition focus:outline-none cursor-pointer">
                ✕
            </button>
        </div>

        <form id="editScheduleForm" method="POST" class="p-8 overflow-y-auto space-y-6">
            @csrf
            @method('PUT')

            <!-- Subject Details -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Subject Name <span class="text-red-600">*</span></label>
                        <input type="text" id="edit_subject_name" name="subject_name" required 
                               class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Subject Code</label>
                        <input type="text" id="edit_subject_code" name="subject_code" 
                               class="w-full py-2.5 px-3 text-sm font-mono font-bold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none uppercase bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Assigned Faculty Instructor <span class="text-red-600">*</span></label>
                    <select id="edit_teacher_id" name="teacher_id" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="">Select Teaching Faculty</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }} ({{ $teacher->id_number ?? $teacher->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grade, Track & Section -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Grade Level <span class="text-red-600">*</span></label>
                        <select id="edit_grade_level" name="grade_level" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                            <option value="Grade 11">Grade 11</option>
                            <option value="Grade 12">Grade 12</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Track <span class="text-red-600">*</span></label>
                        <select id="edit_strand" name="strand" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                            <option value="Academic Track">Academic Track</option>
                            <option value="Technical-Professional">Technical-Professional</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Section <span class="text-red-600">*</span></label>
                        <select id="edit_section" name="section" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                            <option value="Amber">Amber</option>
                            <option value="Crystal">Crystal</option>
                            <option value="Pearl">Pearl</option>
                            <option value="Turquoise">Turquoise</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Day & Time Allocation -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Day Schedule <span class="text-red-600">*</span></label>
                    <select id="edit_day" name="day" required class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Mon / Wed">Mon / Wed</option>
                        <option value="Tue / Thu">Tue / Thu</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Start Time <span class="text-red-600">*</span></label>
                        <input type="time" id="edit_start_time" name="start_time" required class="w-full py-2.5 px-3.5 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">End Time <span class="text-red-600">*</span></label>
                        <input type="time" id="edit_end_time" name="end_time" required class="w-full py-2.5 px-3.5 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-5 border-t-2 border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditScheduleModal()" class="px-5 py-2.5 rounded-xl border-2 border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-extrabold transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#721313] text-white text-xs font-black shadow-md shadow-red-950/20 hover:shadow-lg transition cursor-pointer">
                    Update Schedule
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ================= Success Modal ================= -->
<div id="successModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm {{ session('success') ? 'flex' : 'hidden' }} items-center justify-center p-4 sm:p-6 transition-all duration-300">
    <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-6 sm:p-8 text-center space-y-6">
        
        <div class="relative w-16 h-16 mx-auto">
            <div class="absolute inset-0 rounded-2xl bg-emerald-100 animate-ping opacity-25"></div>
            <div class="relative w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center text-2xl shadow-xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="space-y-1.5">
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Success!</h3>
            <p class="text-xs font-bold text-slate-500 leading-relaxed px-2">
                {{ session('success') }}
            </p>
        </div>

        <div>
            <button type="button" onclick="closeSuccessModal()" class="w-full py-3 rounded-2xl bg-[#8b1818] hover:bg-[#721313] text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-red-950/20 active:scale-[0.98] transition cursor-pointer">
                Okay, Continue
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function openScheduleModal() {
        const modal = document.getElementById('scheduleModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeScheduleModal() {
        const modal = document.getElementById('scheduleModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openEditScheduleModal(schedule) {
        const modal = document.getElementById('editScheduleModal');
        const form = document.getElementById('editScheduleForm');
        
        form.action = `/admin/schedules/${schedule.id}`;

        document.getElementById('edit_subject_name').value = schedule.subject_name || schedule.subject || (schedule.subject_record ? schedule.subject_record.name : '') || '';
        document.getElementById('edit_subject_code').value = schedule.subject_code || (schedule.subject_record ? schedule.subject_record.code : '') || '';
        document.getElementById('edit_teacher_id').value = schedule.teacher_id || '';
        document.getElementById('edit_grade_level').value = schedule.grade_level || 'Grade 11';
        document.getElementById('edit_strand').value = schedule.strand || 'Academic Track';
        document.getElementById('edit_section').value = schedule.section || 'Amber';
        document.getElementById('edit_day').value = schedule.day || schedule.day_of_week || 'Monday';
        document.getElementById('edit_start_time').value = schedule.start_time || '';
        document.getElementById('edit_end_time').value = schedule.end_time || '';

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeEditScheduleModal() {
        const modal = document.getElementById('editScheduleModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function handleOutsideClick(event) {
        const addModal = document.getElementById('scheduleModal');
        const editModal = document.getElementById('editScheduleModal');
        const successModal = document.getElementById('successModal');
        
        if (event.target === addModal) closeScheduleModal();
        if (event.target === editModal) closeEditScheduleModal();
        if (event.target === successModal) closeSuccessModal();
    }

    window.addEventListener('click', handleOutsideClick);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeScheduleModal();
            closeEditScheduleModal();
            closeSuccessModal();
        }
    });
</script>
@endpush
@endsection