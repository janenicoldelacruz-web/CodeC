@extends('layouts.app')

@section('title', 'Admin Dashboard - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-base shadow-xs shrink-0">
                    <i class="fa-solid fa-table-cells-large text-amber-300"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Admin Dashboard</h1>
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase">A.Y. {{ $activeSchoolYear ?? '2027-2028' }}</span>
                    </div>
                    <p class="text-xs text-slate-500 font-bold mt-0.5">
                        <i class="fa-regular fa-calendar text-slate-400 mr-1"></i>
                        {{ \Carbon\Carbon::now()->format('l, F d, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Editable Admin Profile Button -->
            <button type="button" 
                    onclick="openEditProfileModal()"
                    title="Click to edit profile"
                    class="group flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-100 border-2 border-slate-200 hover:border-slate-300 transition text-left bg-white shadow-2xs cursor-pointer">
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white font-black text-xs flex items-center justify-center shadow-xs group-hover:scale-105 transition">
                        {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'D', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-white border border-slate-200 rounded-full flex items-center justify-center text-[9px] text-slate-600 shadow-2xs group-hover:text-[#8b1818]">
                        <i class="fa-solid fa-pen"></i>
                    </span>
                </div>
                <div class="hidden sm:block">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-black text-slate-900 group-hover:text-[#8b1818] transition">
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        </span>
                        <i class="fa-solid fa-pen-to-square text-[10px] text-slate-400 group-hover:text-[#8b1818] transition opacity-0 group-hover:opacity-100"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Administrator</span>
                </div>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="pt-10 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-8 flex-1">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-xs font-bold rounded-2xl flex items-center justify-between shadow-xs w-full">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 text-sm cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <!-- ================= Section 1: KPI Summary Metrics ================= -->
        <div class="space-y-3 w-full">
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-[#8b1818] text-xs"></i>
                    <span>Institutional Metrics Overview</span>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">

                <!-- 1. Total Students Enrolled (Directly links to analytics screen) -->
                <a href="{{ route('admin.analytics.report', 'students') }}" class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-amber-300 transition w-full block text-left group">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Total Students</p>
                            <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalStudents ?? 0) }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 border-2 border-amber-200 text-amber-700 flex items-center justify-center text-xl shadow-xs shrink-0 group-hover:scale-105 transition">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>
                    <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="studentSparkline"></canvas></div>
                    <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                        <span>View Analytics & Filters</span>
                        <i class="fa-solid fa-arrow-right text-amber-500 group-hover:translate-x-1 transition"></i>
                    </div>
                </a>

                <!-- 2. Daily Attendance Rate -->
                <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-red-300 transition w-full">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Attendance Rate</p>
                            <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ $attendanceRate ?? '0%' }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-red-50 border-2 border-red-200 text-[#8b1818] flex items-center justify-center text-xl shadow-xs shrink-0">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                    </div>
                    <div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-2">
                            <div class="bg-[#8b1818] h-full rounded-full transition-all duration-500" style="width: {{ min(100, (float)($attendanceRate ?? 0)) }}%"></div>
                        </div>
                        <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="attendanceSparkline"></canvas></div>
                        <div class="flex items-center justify-between text-xs text-slate-600 font-bold">
                            <span>Present Today</span>
                            <span class="text-[#8b1818] font-mono font-black">{{ $presentTodayCount ?? 0 }} / {{ $totalStudents ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Faculty Evaluation Progress -->
                <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-blue-300 transition w-full">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Faculty Evaluation</p>
                            <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ $evalProgress ?? '0%' }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 border-2 border-blue-200 text-blue-700 flex items-center justify-center text-xl shadow-xs shrink-0">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                    <div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-2">
                            <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ min(100, (float)($evalProgress ?? 0)) }}%"></div>
                        </div>
                        <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="evalSparkline"></canvas></div>
                        <div class="flex items-center justify-between text-xs text-slate-600 font-bold">
                            <span>Student Reviews</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        </div>
                    </div>
                </div>

                <!-- 4. Active SMS Dispatched Today -->
                <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-emerald-300 transition w-full">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">SMS Sent Today</p>
                            <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($activeSMS ?? 0) }}</h3>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-700 flex items-center justify-center text-xl shadow-xs shrink-0">
                            <i class="fa-solid fa-comment-sms"></i>
                        </div>
                    </div>
                    <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="smsSparkline"></canvas></div>
                    <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                        <span>Parent Alerts</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= Section 2: Real-Time Attendance Stream ================= -->
        <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-6 w-full">

            <!-- Table Header & Live Search Box -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b-2 border-slate-100 w-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center text-sm font-black shadow-2xs">
                        <i class="fa-solid fa-tower-broadcast text-[#8b1818]"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Live Gate Attendance Feed</h2>
                        <p class="text-xs text-slate-500 font-bold mt-0.5">Real-time NFC card taps recorded today</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative w-full md:w-80">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="feedSearch" onkeyup="filterFeedTable()" placeholder="Filter live scans..."
                               class="w-full pl-9 pr-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-white transition shadow-2xs">
                    </div>
                </div>
            </div>

            <!-- Feed Table -->
            <div class="overflow-x-auto rounded-2xl border-2 border-slate-200 shadow-2xs w-full">
                <table class="w-full text-left border-collapse" id="feedTable">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-5">Student Name</th>
                            <th class="py-4 px-5 text-center">LRN / School ID</th>
                            <th class="py-4 px-5 text-center">Academic Placement</th>
                            <th class="py-4 px-5 text-center">Time In</th>
                            <th class="py-4 px-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($recentTaps ?? [] as $tap)
                            @php
                                $trackLabel = match((int)($tap->track ?? $tap->strand ?? 0)) {
                                    1 => 'Academic Track',
                                    2 => 'Technical-Professional',
                                    default => 'General Track'
                                };
                                $sectionLabel = match((int)($tap->section ?? 0)) {
                                    1 => 'Amber',
                                    2 => 'Crystal',
                                    3 => 'Pearl',
                                    4 => 'Turquoise',
                                    default => !empty($tap->section) ? 'Sec. ' . $tap->section : null
                                };
                                $gradeLabel = !empty($tap->grade_level) ? 'Grade ' . $tap->grade_level : 'Grade 11';
                            @endphp
                            <tr class="hover:bg-red-50/40 transition tap-row">
                                <!-- Student Info -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-red-100 border border-red-200 text-[#8b1818] text-xs font-black flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($tap->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($tap->last_name ?? 'T', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-slate-900 font-extrabold block leading-tight student-name">{{ $tap->first_name }} {{ $tap->last_name }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- ID / LRN -->
                                <td class="py-4 px-5 text-center text-slate-900 font-mono font-bold text-sm student-id">
                                    {{ $tap->id_number ?? 'N/A' }}
                                </td>

                                <!-- Placement (Grade, Track & Section) -->
                                <td class="py-4 px-5 text-center student-placement">
                                    <div class="space-y-1">
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-800 text-xs font-black border border-slate-300 shadow-2xs inline-block">
                                            {{ $gradeLabel }}
                                        </span>
                                        <div>
                                            <span class="px-2 py-0.5 rounded-md bg-red-50 text-[#8b1818] text-[10px] font-black border border-red-200">
                                                {{ $trackLabel }} @if($sectionLabel) • {{ $sectionLabel }} @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Time In -->
                                <td class="py-4 px-5 text-center text-slate-900 font-mono font-bold text-xs">
                                    @if(!empty($tap->time_in))
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-50 border border-slate-200">
                                            <i class="fa-regular fa-clock text-amber-600 text-xs"></i>
                                            <span>{{ \Carbon\Carbon::parse($tap->time_in)->format('h:i:s A') }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-sans font-semibold text-xs">--:--:--</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-5 text-center">
                                    @if(strtoupper($tap->status ?? '') === 'ON-TIME')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> ON-TIME
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs">LATE</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center text-slate-400 font-medium">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3.5 text-2xl shadow-2xs">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </div>
                                    <p class="text-base font-extrabold text-slate-800">No Attendance Taps Logged Today</p>
                                    <p class="text-xs text-slate-500 font-semibold mt-1">Scans will appear here in real-time as students tap their NFC cards.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</div>

<!-- ================= Modal: Edit Admin Profile ================= -->
<div id="editProfileModal" 
     class="fixed inset-0 z-50 bg-slate-950/60 hidden items-center justify-center p-4 sm:p-6"
     style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden max-h-[92vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="px-8 py-5 border-b-2 border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                    <i class="fa-solid fa-user-gear text-amber-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Administrator Profile</h3>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Manage your administrative credentials and personal details</p>
                </div>
            </div>
            <button type="button" onclick="closeEditProfileModal()" class="w-9 h-9 rounded-xl hover:bg-slate-200/70 text-slate-400 hover:text-slate-700 flex items-center justify-center transition focus:outline-none cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form action="{{ route('admin.profile.update') }}" method="POST" id="adminProfileForm" class="p-8 overflow-y-auto space-y-6">
            @csrf

            <!-- Section 1: Personal & Institutional Information -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-wider">Profile Information</span>
                </div>

                <!-- First & Last Name Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">First Name <span class="text-red-600">*</span></label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required placeholder="First Name"
                                   class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Last Name <span class="text-red-600">*</span></label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required placeholder="Last Name"
                                   class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>
                </div>

                <!-- Admin ID & Contact Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Employee / Admin ID</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-hashtag"></i></span>
                            <input type="text" name="id_number" value="{{ old('id_number', auth()->user()->id_number) }}" placeholder="ADM-2026-001"
                                   class="w-full py-2.5 px-3 text-sm font-mono font-bold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Contact Number</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-phone"></i></span>
                            <input type="text" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" placeholder="09171234567"
                                   class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Institutional Email Address <span class="text-red-600">*</span></label>
                    <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                        <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required placeholder="admin@siatrack.edu.ph"
                               class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                    </div>
                </div>
            </div>

            <!-- Section 2: Security & Password Management -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-wider">Authentication & Security</span>
                    <span class="text-[10px] font-bold text-slate-400 italic">Leave empty to keep current password</span>
                </div>

                <!-- Current Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Current Password</label>
                    <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                        <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-key"></i></span>
                        <input type="password" id="current_password_field" name="current_password" placeholder="Required only if updating password"
                               class="w-full py-2.5 px-3 pr-10 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        <button type="button" onclick="togglePasswordVisibility('current_password_field', this)" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password & Confirm Password Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">New Password</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" id="new_password_field" name="password" minlength="8" placeholder="Minimum 8 characters"
                                   class="w-full py-2.5 px-3 pr-10 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                            <button type="button" onclick="togglePasswordVisibility('new_password_field', this)" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Confirm New Password</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#8b1818] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-lock-open"></i></span>
                            <input type="password" id="confirm_password_field" name="password_confirmation" minlength="8" placeholder="Re-enter new password"
                                   class="w-full py-2.5 px-3 pr-10 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                            <button type="button" onclick="togglePasswordVisibility('confirm_password_field', this)" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-5 border-t-2 border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditProfileModal()" 
                        class="px-5 py-2.5 rounded-xl border-2 border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-extrabold cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#721313] text-white text-xs font-black shadow-md shadow-red-950/20 flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function loadCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(loadCharts, 50);
            return;
        }

        const chartOpts = {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            elements: { point: { radius: 0 } }
        };

        const s = document.getElementById('studentSparkline');
        if (s && !s.dataset.init) {
            s.dataset.init = "1";
            new Chart(s, {
                type: 'line',
                data: {
                    labels: ['1','2','3','4','5','6','7'],
                    datasets: [{ data: [0, 0, 1, 1, 1, 1, 1], borderColor: '#f59e0b', backgroundColor: 'rgba(245, 158, 11, 0.12)', borderWidth: 1.5, fill: true, tension: 0.3 }]
                },
                options: chartOpts
            });
        }

        const a = document.getElementById('attendanceSparkline');
        if (a && !a.dataset.init) {
            a.dataset.init = "1";
            new Chart(a, {
                type: 'line',
                data: {
                    labels: ['M','T','W','T','F'],
                    datasets: [{ data: [0, 0, 0, 0, 0], borderColor: '#e11d48', backgroundColor: 'rgba(225, 29, 72, 0.12)', borderWidth: 1.5, fill: true, tension: 0.3 }]
                },
                options: chartOpts
            });
        }

        const e = document.getElementById('evalSparkline');
        if (e && !e.dataset.init) {
            e.dataset.init = "1";
            new Chart(e, {
                type: 'line',
                data: {
                    labels: ['1','2','3','4'],
                    datasets: [{ data: [0, 0, 0, 0], borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.12)', borderWidth: 1.5, fill: true, tension: 0.3 }]
                },
                options: chartOpts
            });
        }

        const sms = document.getElementById('smsSparkline');
        if (sms && !sms.dataset.init) {
            sms.dataset.init = "1";
            new Chart(sms, {
                type: 'bar',
                data: {
                    labels: ['1','2','3','4','5','6'],
                    datasets: [{ data: [0, 0, 0, 0, 0, 0], backgroundColor: 'rgba(16, 185, 129, 0.5)', borderRadius: 2 }]
                },
                options: chartOpts
            });
        }
    }
    loadCharts();
});
</script>
<!-- METRIC DETAIL MODAL CONTAINER -->
@php
    $modalStudents = \Illuminate\Support\Facades\DB::table('users')
        ->where('role_id', 3)
        ->select('id', 'first_name', 'last_name', 'email', 'id_number', 'created_at')
        ->latest('created_at')->take(8)->get();

    $modalAttendance = \Illuminate\Support\Facades\Schema::hasTable('attendance_logs')
        ? \Illuminate\Support\Facades\DB::table('attendance_logs')
            ->leftJoin('users', 'attendance_logs.user_id', '=', 'users.id')
            ->select('attendance_logs.*', 'users.first_name', 'users.last_name', 'users.id_number')
            ->whereDate('attendance_logs.created_at', now()->today())
            ->latest('attendance_logs.created_at')->take(8)->get()
        : collect();

    $modalFaculty = \Illuminate\Support\Facades\DB::table('users')
        ->where('role_id', 2)
        ->select('id', 'first_name', 'last_name', 'email', 'id_number')
        ->take(8)->get();
@endphp

<div id="metricDataModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
            <div class="flex items-center gap-3">
                <div id="modalIconBox" class="w-10 h-10 rounded-2xl flex items-center justify-center text-base"></div>
                <div>
                    <h3 id="modalTitle" class="text-base font-black text-slate-800"></h3>
                    <p id="modalSubtitle" class="text-xs text-slate-400 font-medium"></p>
                </div>
            </div>
            <button onclick="closeMetricModal()" class="w-8 h-8 rounded-full bg-slate-200/60 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto flex-1 space-y-4">
            <!-- 1. Students Content -->
            <div id="modalSection_students" class="metric-modal-section hidden">
                <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="font-bold text-slate-500">Enrolled Students (Recent)</span>
                    <span class="font-black text-[#8b1818]">{{ count($modalStudents) }} student(s) loaded</span>
                </div>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Student Name</th>
                                <th class="p-3">LRN / ID</th>
                                <th class="p-3">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($modalStudents as $st)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-800">{{ $st->first_name }} {{ $st->last_name }}</td>
                                <td class="p-3 text-slate-500 font-mono text-[11px]">{{ $st->id_number ?? 'N/A' }}</td>
                                <td class="p-3 text-slate-500">{{ $st->email }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-400">Walang estudyanteng nakatala.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Attendance Content -->
            <div id="modalSection_attendance" class="metric-modal-section hidden">
                <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="font-bold text-slate-500">Today's Gate Tap Records</span>
                    <span class="font-black text-red-600">{{ count($modalAttendance) }} tap(s) today</span>
                </div>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Student</th>
                                <th class="p-3">Time In</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($modalAttendance as $at)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-800">{{ $at->first_name ?? 'Student' }} {{ $at->last_name ?? '' }}</td>
                                <td class="p-3 text-slate-500">{{ \Carbon\Carbon::parse($at->created_at)->format('h:i A') }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($at->status ?? '') == 'LATE' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">A.Y. {{ $activeSchoolYear ?? '2027-2028' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-400">Walang attendance tap na naitala ngayong araw.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Faculty Evaluation Content -->
            <div id="modalSection_evaluation" class="metric-modal-section hidden">
                <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="font-bold text-slate-500">Faculty Members & Performance</span>
                    <span class="font-black text-blue-600">{{ count($modalFaculty) }} instructors</span>
                </div>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Instructor</th>
                                <th class="p-3">School ID</th>
                                <th class="p-3">Evaluation Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($modalFaculty as $fa)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-800">{{ $fa->first_name }} {{ $fa->last_name }}</td>
                                <td class="p-3 text-slate-500 font-mono text-[11px]">{{ $fa->id_number ?? 'FAC-N/A' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">Active Cycle</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-400">Walang guro na nakarehistro.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. SMS Alerts Content -->
            <div id="modalSection_sms" class="metric-modal-section hidden">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-900 text-xs space-y-2">
                    <div class="flex items-center justify-between font-bold">
                        <span>GSM / SMS Notification Gateway</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-black">ONLINE</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">Awtomatikong nagpapadala ng SMS alert sa mga magulang tuwing may matatala na NFC scan sa gate kiosk.</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between font-bold text-slate-600">
                        <span>Dispatched Today:</span>
                        <span class="font-black text-slate-800">{{ $smsSentToday ?? 0 }} alerts</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-600">
                        <span>Delivery Success Rate:</span>
                        <span class="font-black text-emerald-600">100%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/60 flex justify-end">
            <button onclick="closeMetricModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openMetricModal(type) {
    const modal = document.getElementById('metricDataModal');
    const title = document.getElementById('modalTitle');
    const sub = document.getElementById('modalSubtitle');
    const iconBox = document.getElementById('modalIconBox');

    document.querySelectorAll('.metric-modal-section').forEach(el => el.classList.add('hidden'));

    if (type === 'attendance') {
        title.innerText = "Attendance Analytics & Gate Logs";
        sub.innerText = "Breakdown ng mga live taps at attendance status para sa araw na ito";
        iconBox.className = "w-10 h-10 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-base";
        iconBox.innerHTML = '<i class="fa-solid fa-clipboard-check"></i>';
        document.getElementById('modalSection_attendance').classList.remove('hidden');
    } else if (type === 'evaluation') {
        title.innerText = "Faculty Evaluation Summary";
        sub.innerText = "Feedback overview at listahan ng mga guro sa academic cycle";
        iconBox.className = "w-10 h-10 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-base";
        iconBox.innerHTML = '<i class="fa-solid fa-chalkboard-user"></i>';
        document.getElementById('modalSection_evaluation').classList.remove('hidden');
    } else if (type === 'sms') {
        title.innerText = "Parent SMS Alerts Dispatch";
        sub.innerText = "Status ng automated text notifications mula sa NFC Gate System";
        iconBox.className = "w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-base";
        iconBox.innerHTML = '<i class="fa-solid fa-comment-sms"></i>';
        document.getElementById('modalSection_sms').classList.remove('hidden');
    }

    modal.classList.remove('hidden');
}

function closeMetricModal() {
    const modal = document.getElementById('metricDataModal');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMetricModal();
});

// Awtomatikong ikabit ang click listener at pointer cursor sa natitirang 3 cards (Attendance, Evaluation, SMS)
function bindDashboardCards() {
    const targets = [
        { key: 'ATTENDANCE RATE', type: 'attendance' },
        { key: 'FACULTY EVALUATION', type: 'evaluation' },
        { key: 'SMS SENT TODAY', type: 'sms' }
    ];

    targets.forEach(t => {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
        let node;
        while (node = walker.nextNode()) {
            if (node.nodeValue && node.nodeValue.trim().toUpperCase() === t.key) {
                const card = node.parentElement.closest('.bg-white');
                if (card && !card.dataset.metricBound) {
                    card.dataset.metricBound = "true";
                    card.style.cursor = "pointer";
                    card.style.transition = "transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease";
                    
                    card.addEventListener('mouseenter', () => {
                        card.style.transform = "translateY(-3px)";
                        card.style.boxShadow = "0 10px 25px -5px rgba(0, 0, 0, 0.08)";
                        card.style.borderColor = "#8b1818";
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.transform = "none";
                        card.style.boxShadow = "";
                        card.style.borderColor = "";
                    });
                    card.addEventListener('click', (e) => {
                        e.preventDefault();
                        openMetricModal(t.type);
                    });
                }
                break;
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindDashboardCards);
} else {
    bindDashboardCards();
}
</script>
<!-- END METRIC MODAL POPUP SYSTEM -->
@endsection

@push('scripts')
<script>
    function openEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function togglePasswordVisibility(fieldId, btn) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            field.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }

    function filterFeedTable() {
        const searchInput = document.getElementById('feedSearch');
        if (!searchInput) return;

        const query = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.tap-row');

        rows.forEach(row => {
            const name = row.querySelector('.student-name')?.innerText.toLowerCase() || '';
            const id = row.querySelector('.student-id')?.innerText.toLowerCase() || '';
            const placement = row.querySelector('.student-placement')?.innerText.toLowerCase() || '';

            if (name.includes(query) || id.includes(query) || placement.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editProfileModal');
        
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeEditProfileModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeEditProfileModal();
            }
        });

        @if($errors->any())
            openEditProfileModal();
        @endif
    });
</script>
@endpush