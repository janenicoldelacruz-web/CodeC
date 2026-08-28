<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Class Schedule Matrix - Teacher Portal - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sia-card { background: #ffffff; border: 2px solid #e2e8f0; border-radius: 24px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03); transition: all 0.2s ease; }
        .timetable-cell { min-height: 260px; vertical-align: top; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex">

    <!-- REUSABLE SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 flex flex-col min-w-0 ml-72">
        
        <!-- Top Navigation Header -->
        <header class="bg-white border-b-2 border-slate-200/80 px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Assigned Class Schedule</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Southern Isabela Academy • Faculty Timetable Matrix</p>
            </div>
            
            <div class="flex items-center gap-4 flex-wrap">
                <!-- Search Schedule Form -->
                <form method="GET" action="{{ route('teacher.schedules') }}" class="relative flex items-center w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search Subject..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-white transition shadow-2xs">
                </form>

                <!-- Profile Trigger Button -->
                <button type="button" onclick="openProfileModal()" class="flex items-center gap-3 pl-4 border-l-2 border-slate-200 hover:opacity-80 transition cursor-pointer">
                    @if(!empty($teacher->photo))
                        <img src="{{ asset('storage/' . $teacher->photo) }}" class="w-10 h-10 rounded-2xl object-cover border-2 border-amber-300 shadow-2xs">
                    @else
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 border border-amber-300 text-amber-900 flex items-center justify-center font-black text-xs">
                            {{ strtoupper(substr($teacher->first_name ?? 'T', 0, 1)) }}{{ strtoupper(substr($teacher->last_name ?? 'F', 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-extrabold text-slate-900 leading-tight">Prof. {{ $teacher->first_name }} {{ $teacher->last_name }}</span>
                        <span class="text-[10px] font-bold text-amber-700">Edit Faculty Profile <i class="fa-solid fa-angle-right text-[8px]"></i></span>
                    </div>
                </button>
            </div>
        </header>

        <!-- Page Body Content -->
        <div class="p-8 max-w-7xl w-full mx-auto space-y-8 flex-1">
            
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Master Matrix Card -->
            <div class="sia-card p-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-100 text-[#8b1818] flex items-center justify-center text-sm shadow-2xs">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-black text-slate-900 tracking-tight">Master Weekly Timetable Matrix</h2>
                            <p class="text-[11px] text-slate-400 font-bold">Faculty Load Allocation Grid (Click any subject card to view its student class list)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-red-50 border border-red-200 rounded-full text-xs font-black text-[#8b1818] shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Department: {{ $teacher->strand ?? 'General Faculty' }}
                        </span>
                    </div>
                </div>

                <!-- Matrix Grid Container -->
                <div class="border-2 border-slate-200 rounded-3xl overflow-hidden bg-white shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-center text-xs">
                            <thead>
                                <tr class="bg-[#8b1818] text-white uppercase font-black tracking-wider text-[11px]">
                                    <th class="py-4 px-3 border-r border-red-900/40 w-1/5">Monday</th>
                                    <th class="py-4 px-3 border-r border-red-900/40 w-1/5">Tuesday</th>
                                    <th class="py-4 px-3 border-r border-red-900/40 w-1/5">Wednesday</th>
                                    <th class="py-4 px-3 border-r border-red-900/40 w-1/5">Thursday</th>
                                    <th class="py-4 px-3 w-1/5">Friday</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-semibold text-slate-800">
                                @php
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                                @endphp

                                <tr>
                                    @foreach($days as $day)
                                        @php
                                            $daySchedules = collect($mySchedules ?? [])->filter(function($sched) use ($day) {
                                                $schedDay = trim($sched->day ?? $sched->day_of_week ?? '');
                                                return (strcasecmp($schedDay, $day) === 0 || strcasecmp($schedDay, 'Daily') === 0 || stripos($schedDay, $day) !== false);
                                            })->sortBy('start_time');
                                        @endphp

                                        <td class="p-3 border-r border-slate-200 last:border-r-0 timetable-cell align-top space-y-3 bg-slate-50/20">
                                            @forelse($daySchedules as $matchedSched)
                                                <!-- LARGER, PROMINENT CLASS CARD -->
                                                <a href="{{ route('teacher.schedule.students', $matchedSched->id) }}" 
                                                   class="w-full p-4 rounded-2xl bg-white border-2 border-slate-200 text-left flex flex-col justify-between shadow-xs transition hover:border-[#8b1818] hover:shadow-md hover:scale-[1.01] block group cursor-pointer relative overflow-hidden">
                                                    
                                                    <!-- Left Accent Bar -->
                                                    <div class="absolute left-0 inset-y-0 w-1.5 bg-[#8b1818]"></div>

                                                    <div class="pl-3 space-y-2">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="text-[10px] font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                                                {{ $matchedSched->subject_code ?? optional($matchedSched->subjectRecord)->code ?? 'SUBJ' }}
                                                            </span>
                                                            <span class="text-[10px] font-black text-[#8b1818] bg-red-50 px-2 py-0.5 rounded-md border border-red-200 shadow-2xs">
    Sec: {{ $matchedSched->section ?? $matchedSched->section_name ?? optional($matchedSched->academicSection)->name ?? optional($matchedSched->academicSection)->section_name ?? 'Amber' }}
</span>
                                                        </div>

                                                        <h4 class="text-sm font-black text-slate-900 group-hover:text-[#8b1818] tracking-tight leading-snug transition">
                                                            {{ $matchedSched->subject_name ?? $matchedSched->subject ?? optional($matchedSched->subjectRecord)->name ?? 'Unnamed Subject' }}
                                                        </h4>
                                                    </div>

                                                    <div class="mt-3 pl-3 pt-2.5 border-t border-slate-100 text-xs font-bold text-slate-500 flex items-center justify-between">
                                                        <span class="font-mono text-[11px] tracking-tight text-slate-700">
                                                            <i class="fa-regular fa-clock mr-1 text-amber-600"></i>{{ date('h:i A', strtotime($matchedSched->start_time)) }} - {{ date('h:i A', strtotime($matchedSched->end_time)) }}
                                                        </span>
                                                        <span class="text-xs text-[#8b1818] font-black group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                                        </span>
                                                    </div>
                                                </a>
                                            @empty
                                                <div class="h-full w-full flex items-center justify-center text-slate-300 text-xs py-16 italic font-normal">
                                                    No classes
                                                </div>
                                            @endforelse
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ================= EDIT FACULTY PROFILE MODAL ================= -->
    <div id="teacher_profile_modal" class="hidden fixed inset-0 items-center justify-center p-4 sm:p-6 bg-slate-950/60 transition-all duration-200 z-50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden transform transition-all p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-100 text-[#8b1818] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Faculty Profile</h3>
                        <p class="text-xs text-slate-400 font-bold">Update your institutional registration details</p>
                    </div>
                </div>
                <button type="button" onclick="closeProfileModal()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Faculty / Employee ID *</label>
                        <input type="text" name="id_number" value="{{ old('id_number', $teacher->id_number) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-slate-100 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-white">
                            <option value="">Select Gender</option>
                            <option value="1" {{ (int)$teacher->gender === 1 ? 'selected' : '' }}>Male</option>
                            <option value="2" {{ (int)$teacher->gender === 2 ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Contact Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $teacher->phone_number) }}" placeholder="09xxxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Password *</label>
                        <input type="password" name="password" placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                        <span class="block text-[10px] text-slate-400 mt-0.5">Leave blank to keep current</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="relative shrink-0">
                        @if(!empty($teacher->photo))
                            <img id="profile_preview" src="{{ asset('storage/' . $teacher->photo) }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-300 shadow-sm">
                        @else
                            <div id="profile_preview_fallback" class="w-16 h-16 rounded-2xl bg-amber-100 border border-amber-300 text-amber-900 flex items-center justify-center font-black text-xl">
                                {{ strtoupper(substr($teacher->first_name ?? 'T', 0, 1)) }}{{ strtoupper(substr($teacher->last_name ?? 'F', 0, 1)) }}
                            </div>
                            <img id="profile_preview" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-300 shadow-sm hidden">
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Faculty Profile Photo</label>
                        <input type="file" name="photo" accept="image/jpeg,image/png" onchange="previewImage(event)" 
                               class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#8b1818] file:text-white hover:file:bg-[#731414] cursor-pointer">
                        <span class="block text-[10px] text-slate-400 mt-1 font-medium">Optional, JPG/PNG up to 2MB</span>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeProfileModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#731414] text-white text-xs font-black uppercase tracking-wider transition shadow-md shadow-red-950/20 active:scale-[0.98] cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SCRIPT -->
    <script>
        function openProfileModal() {
            const modal = document.getElementById('teacher_profile_modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeProfileModal() {
            const modal = document.getElementById('teacher_profile_modal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('profile_preview');
                const fallback = document.getElementById('profile_preview_fallback');
                if (preview) {
                    preview.src = reader.result;
                    preview.classList.remove('hidden');
                }
                if (fallback) fallback.classList.add('hidden');
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>