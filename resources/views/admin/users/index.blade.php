@extends('layouts.app')

@section('title', 'User Management - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-slate-100 to-zinc-100 relative"
     x-data="{ exportOpen: false }">

    <!-- Top Header Bar -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-6 lg:px-10 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#8b1818] to-rose-700 text-white flex items-center justify-center text-base shadow-lg shadow-red-950/25 shrink-0">
                <i class="fa-solid fa-users text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-slate-900 tracking-tight">User Management</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Manage institutional student, faculty, director, and administrator directory records</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Export CSV Dropdown (Alpine.js Powered) -->
            <div class="relative" @click.outside="exportOpen = false">
                <button type="button" @click="exportOpen = !exportOpen" class="inline-flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-black uppercase tracking-wider border-2 border-slate-200 shadow-2xs hover:border-slate-300 transition cursor-pointer">
                    <i class="fa-solid fa-file-csv text-emerald-600 text-base"></i> 
                    <span>Export CSV</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 transition-transform" :class="exportOpen ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="exportOpen" x-transition class="absolute right-0 mt-2.5 w-72 bg-white border-2 border-slate-200 rounded-2xl shadow-2xl z-50 py-2 overflow-hidden" style="display: none;" x-cloak>
                    <div class="px-4 py-2 border-b border-slate-100 text-[10px] uppercase font-black tracking-wider text-slate-400 bg-slate-50/50">Select Directory Export</div>
                    @if(Route::has('admin.users.export'))
                        <a href="{{ route('admin.users.export', ['type' => 'students']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-red-50 hover:text-[#8b1818] transition">
                            <div class="w-8 h-8 rounded-xl bg-red-100 text-[#8b1818] flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div><p class="font-extrabold leading-snug">Students Directory</p><span class="text-[10px] text-slate-400 font-medium">Export enrolled students</span></div>
                        </a>
                        <a href="{{ route('admin.users.export', ['type' => 'faculty']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-900 transition">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-chalkboard-user"></i></div>
                            <div><p class="font-extrabold leading-snug">Faculty Members</p><span class="text-[10px] text-slate-400 font-medium">Export teaching staff</span></div>
                        </a>
                        <a href="{{ route('admin.users.export', ['type' => 'directors']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-purple-50 hover:text-purple-900 transition">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-user-tie"></i></div>
                            <div><p class="font-extrabold leading-snug">Directors</p><span class="text-[10px] text-slate-400 font-medium">Export directors</span></div>
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="{{ route('admin.users.export', ['type' => 'all']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs shrink-0"><i class="fa-solid fa-users"></i></div>
                            <div><p class="font-extrabold leading-snug">All Users Combined</p><span class="text-[10px] text-slate-400 font-medium">Excluding system admin</span></div>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Add New User Link -->
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/20 transition cursor-pointer">
                <i class="fa-solid fa-user-plus text-sm text-amber-300"></i>
                <span>Add New User</span>
            </a>
        </div> 
    </header>

    <!-- Main Content Body -->
    <main class="p-6 lg:p-10 w-full max-w-7xl mx-auto space-y-8 flex-1">
        
        <!-- Controls: Tabs + Search -->
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between w-full">
            <div class="inline-flex flex-wrap p-1.5 bg-slate-200/70 rounded-2xl border-2 border-slate-200 text-xs font-black gap-1 shadow-2xs">
                <a href="{{ route('admin.users.index', ['role' => 'all', 'search' => $search]) }}" class="px-4 py-2.5 rounded-xl transition {{ $roleFilter === 'all' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">All ({{ $totalUsers ?? 0 }})</a>
                <a href="{{ route('admin.users.index', ['role' => 'admin', 'search' => $search]) }}" class="px-4 py-2.5 rounded-xl transition {{ $roleFilter === 'admin' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">Admins ({{ $adminCount ?? 0 }})</a>
                <a href="{{ route('admin.users.index', ['role' => 'director', 'search' => $search]) }}" class="px-4 py-2.5 rounded-xl transition {{ $roleFilter === 'director' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">Directors ({{ $directorCount ?? 0 }})</a>
                <a href="{{ route('admin.users.index', ['role' => 'faculty', 'search' => $search]) }}" class="px-4 py-2.5 rounded-xl transition {{ in_array($roleFilter, ['faculty', 'teacher']) ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">Faculty ({{ $teacherCount ?? 0 }})</a>
                <a href="{{ route('admin.users.index', ['role' => 'student', 'search' => $search]) }}" class="px-4 py-2.5 rounded-xl transition {{ $roleFilter === 'student' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">Students ({{ $studentCount ?? 0 }})</a>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 w-full md:w-96">
                <input type="hidden" name="role" value="{{ $roleFilter }}">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, ID, or email..." class="w-full pl-10 pr-4 py-3 text-xs font-bold bg-slate-50/70 border-2 border-slate-200/80 rounded-2xl focus:border-[#8b1818] focus:bg-white outline-none transition shadow-2xs">
                </div>
                @if(!empty($search))
                    <a href="{{ route('admin.users.index', ['role' => $roleFilter]) }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-black transition shrink-0 shadow-2xs">Clear</a>
                @endif
            </form>
        </div>

        <!-- ================= SECTION: ADMINISTRATORS ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'admin')
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-shield-halved"></i></div>
                    <div><h2 class="text-base font-black text-slate-900 tracking-tight">Administrators</h2><p class="text-xs text-slate-500 font-bold mt-0.5">System administration accounts</p></div>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300">Total Admins: {{ isset($admins) ? $admins->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-black tracking-wider text-xs border-y border-slate-200/80">
                            <th class="py-4 px-6">Name</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-800">
                        @forelse($admins ?? [] as $adminUser)
                        <tr class="hover:bg-amber-50/20 transition">
                            <td class="py-4 px-6 font-extrabold text-slate-900 text-xs uppercase">{{ $adminUser->first_name ?? '' }} {{ $adminUser->last_name ?? $adminUser->name }}</td>
                            <td class="py-4 px-6 text-slate-600 text-xs">{{ $adminUser->email }}</td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $adminUser->id) }}" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs border border-slate-200 transition" title="Edit Admin">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    @if(auth()->id() !== $adminUser->id)
                                    <button type="button" onclick="openDeleteModal('{{ $adminUser->id }}', '{{ addslashes(($adminUser->first_name ?? '') . ' ' . ($adminUser->last_name ?? $adminUser->name)) }}', 'Administrator')" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] flex items-center justify-center text-xs border border-red-200 cursor-pointer transition" title="Delete Admin">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-12 text-center text-slate-400 font-bold text-xs">No Administrator accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($admins) && $admins->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/70">
                    {{ $admins->links() }}
                </div>
            @endif
        </div>
        @endif

        <!-- ================= SECTION: DIRECTORS ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'director')
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-user-tie"></i></div>
                    <div><h2 class="text-base font-black text-slate-900 tracking-tight">Directors</h2><p class="text-xs text-slate-500 font-bold mt-0.5">Director personnel directory</p></div>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300">Total Directors: {{ isset($directors) ? $directors->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-black tracking-wider text-xs border-y border-slate-200/80">
                            <th class="py-4 px-6">Employee ID</th>
                            <th class="py-4 px-6">Name</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-800">
                        @forelse($directors ?? [] as $directorUser)
                        <tr class="hover:bg-purple-50/20 transition">
                            <td class="py-4 px-6 font-bold text-slate-900 text-xs">{{ $directorUser->id_number ?? 'N/A' }}</td>
                            <td class="py-4 px-6 font-extrabold text-slate-900 text-xs uppercase">{{ $directorUser->first_name ?? '' }} {{ $directorUser->last_name ?? $directorUser->name }}</td>
                            <td class="py-4 px-6 text-slate-600 text-xs">{{ $directorUser->email }}</td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $directorUser->id) }}" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs border border-slate-200 transition" title="Edit Director">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <button type="button" onclick="openDeleteModal('{{ $directorUser->id }}', '{{ addslashes(($directorUser->first_name ?? '') . ' ' . ($directorUser->last_name ?? $directorUser->name)) }}', 'Director')" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] flex items-center justify-center text-xs border border-red-200 cursor-pointer transition" title="Delete Director">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-slate-400 font-bold text-xs">No Director accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($directors) && $directors->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/70">
                    {{ $directors->links() }}
                </div>
            @endif
        </div>
        @endif

        <!-- ================= SECTION: FACULTY DIRECTORY ================= -->
        @if($roleFilter === 'all' || in_array($roleFilter, ['faculty', 'teacher']))
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div><h2 class="text-base font-black text-slate-900 tracking-tight">Faculty Members</h2><p class="text-xs text-slate-500 font-bold mt-0.5">Teaching personnel and staff directory</p></div>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300">Total Faculty: {{ isset($faculty) ? $faculty->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-black tracking-wider text-xs border-y border-slate-200/80">
                            <th class="py-4 px-6">Faculty ID</th>
                            <th class="py-4 px-6">Teacher Name</th>
                            <th class="py-4 px-6">Email</th>
                            <th class="py-4 px-6">Role Type</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-800">
                        @forelse($faculty ?? [] as $member)
                        <tr class="hover:bg-slate-50/50 transition align-top">
                            <!-- Faculty ID -->
                            <td class="py-4 px-6 font-bold text-slate-900 text-xs pt-5">
                                {{ $member->id_number ?? 'N/A' }}
                            </td>

                            <!-- Teacher Name -->
                            <td class="py-4 px-6 pt-5">
                                <div class="font-extrabold text-slate-900 text-xs uppercase">{{ $member->first_name ?? 'N/A' }} {{ $member->last_name ?? '' }}</div>
                            </td>

                            <!-- Email (Separated Column) -->
                            <td class="py-4 px-6 text-slate-600 text-xs pt-5">
                                {{ $member->email ?? 'N/A' }}
                            </td>

                            <!-- Role Type -->
                            <td class="py-4 px-6 pt-5">
                                @php
                                    $rawRole = $member->designation ?? $member->role_type ?? $member->role ?? $member->type ?? null;
                                    $cleanRole = '';
                                    if (is_object($rawRole)) {
                                        $cleanRole = $rawRole->name ?? $rawRole->description ?? '';
                                    } elseif (is_array($rawRole)) {
                                        $cleanRole = $rawRole['name'] ?? $rawRole['description'] ?? '';
                                    } else {
                                        $cleanRole = $rawRole ?? '';
                                    }
                                    
                                    $isAdviser = (stripos($cleanRole, 'adviser') !== false) || (!empty($member->section) && stripos($cleanRole, 'subject') === false);
                                    
                                    $gradeLevel = '';
                                    if (!empty($member->grade_level)) {
                                        $gradeLevel = stripos($member->grade_level, 'grade') !== false ? $member->grade_level : 'Grade ' . $member->grade_level;
                                    } elseif (is_object($member->section) && !empty($member->section->grade_level)) {
                                        $gradeLevel = 'Grade ' . $member->section->grade_level;
                                    }

                                    $sectionName = '';
                                    if (is_object($member->section)) {
                                        $sectionName = $member->section->name ?? '';
                                    } elseif (!empty($member->section)) {
                                        $sectionName = $member->section;
                                    }
                                    
                                    if (strlen(trim($sectionName)) === 1) {
                                        $sectionName = strtoupper($sectionName);
                                    }
                                @endphp

                                <div class="font-bold text-slate-800 text-xs">
                                    {{ $isAdviser ? 'Class Adviser' : 'Subject Teacher' }}
                                </div>

                                @if($isAdviser && (!empty($gradeLevel) || !empty($sectionName)))
                                    <div class="mt-1 text-xs font-bold text-slate-500">
                                        {{ trim($gradeLevel . (!empty($sectionName) ? '-' . $sectionName : '')) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-right whitespace-nowrap pt-4">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs border border-slate-200 transition" title="Edit Faculty">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <button type="button" onclick="openDeleteModal('{{ $member->id }}', '{{ addslashes(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')) }}', 'Faculty Member')" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] flex items-center justify-center text-xs border border-red-200 cursor-pointer transition" title="Delete Faculty">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-bold text-xs">No Faculty accounts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($faculty) && $faculty->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/70">
                    {{ $faculty->links() }}
                </div>
            @endif
        </div>
        @endif

        <!-- ================= SECTION: STUDENTS DIRECTORY ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'student')
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-red-100 text-[#8b1818] flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div><h2 class="text-base font-black text-slate-900 tracking-tight">Enrolled Students</h2><p class="text-xs text-slate-500 font-bold mt-0.5">Active enrolled student directory</p></div>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300">Total Students: {{ isset($students) ? $students->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-black tracking-wider text-xs border-y border-slate-200/80">
                            <th class="py-4 px-6">LRN</th>
                            <th class="py-4 px-6">Student Name</th>
                            <th class="py-4 px-6 text-center">Academic Placement</th>
                            <th class="py-4 px-6 text-center">NFC Card</th>
                            <th class="py-4 px-6">Parent Contact</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-800">
                        @forelse($students ?? [] as $student)
                        <tr class="hover:bg-red-50/20 transition">
                            <td class="py-4 px-6 font-bold text-slate-900 text-xs">{{ $student->id_number ?? 'Not Set' }}</td>
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-slate-900 text-xs uppercase">{{ $student->first_name }} {{ $student->last_name }}</div>
                                <span class="text-xs text-slate-400">{{ $student->email }}</span>
                            </td>
<!-- Placement Column -->
<td class="py-4 px-6 text-center">
    @php
        $grade = (!empty($student->grade_level) && $student->grade_level != 0) 
            ? (stripos($student->grade_level, 'grade') !== false ? strtoupper($student->grade_level) : 'GRADE ' . strtoupper($student->grade_level)) 
            : 'GRADE UNSET';
        
        $sectionName = $student->section ?? '';
        $strandName = $student->strand ?? $student->track ?? '';
    @endphp

    <div class="inline-flex flex-col items-center justify-center gap-0.5">
        <!-- Grade Level at Section sa Itaas -->
        <span class="font-black text-slate-900 uppercase text-xs">
            {{ $grade }}{{ !empty($sectionName) ? ' — ' . strtoupper($sectionName) : '' }}
        </span>

        <!-- Strand sa Ibaba (Kung mayroon man) -->
        @if(!empty($strandName))
            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-md font-black text-[10px] uppercase tracking-wider">
                {{ strtoupper($strandName) }}
            </span>
        @else
            <span class="text-[10px] text-slate-400 font-bold uppercase">NO STRAND</span>
        @endif
    </div>
</td>
                            <!-- NFC Card Column -->
                            <td class="py-4 px-6 text-center font-bold text-xs text-slate-800">
                                @if(optional($student->nfcCard)->tag_id)
                                    <span>{{ $student->nfcCard->tag_id }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">No Card</span>
                                @endif
                            </td>
                            <!-- Parent Contact Column -->
                            <td class="py-4 px-6 text-slate-700 font-bold text-xs">
                                {{ $student->parent_phone_number ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.nfc.binding') }}?student_id={{ $student->id }}&student_name={{ urlencode($student->last_name . ', ' . $student->first_name . ' (' . ($student->id_number ?? 'No LRN') . ')') }}" 
                                       class="w-9 h-9 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 flex items-center justify-center text-xs border border-amber-200 transition" 
                                       title="Bind NFC Card">
                                        <i class="fa-solid fa-wifi text-xs text-amber-700"></i>
                                    </a>

                                    <a href="{{ route('admin.users.edit', $student->id) }}" 
                                       class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center text-xs border border-slate-200 transition" 
                                       title="Edit Student">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    <button type="button" onclick="openDeleteModal('{{ $student->id }}', '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', 'Student')" 
                                            class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] flex items-center justify-center text-xs border border-red-200 cursor-pointer transition" 
                                            title="Delete Student">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400 font-bold text-xs">No Student accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($students) && $students->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/70">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
        @endif
    </main>
</div>

<!-- ================= LARGE DELETE CONFIRMATION MODAL (Uniform Blur Effect) ================= -->
<div id="delete_confirm_modal" class="hidden fixed inset-0 items-center justify-center p-4 sm:p-6 bg-slate-950/60 backdrop-blur-md transition-all duration-200" style="z-index: 99999;">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all p-8 text-center space-y-5 animate-in fade-in zoom-in-95">
        <div class="w-16 h-16 bg-rose-50 border border-rose-200 text-rose-600 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-sm">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <div class="space-y-1.5">
            <h3 class="text-base font-black text-slate-900 tracking-tight">Confirm Account Deletion</h3>
            <p class="text-xs font-semibold text-slate-500 leading-relaxed">
                Are you sure you want to permanently remove <span id="delete_user_name_display" class="font-black text-slate-900"></span> (<span id="delete_user_role_display" class="font-black text-[#8b1818]"></span>)?
            </p>
        </div>
        <form id="delete_user_form" method="POST" action="" class="pt-2">
            @csrf
            @method('DELETE')
            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeDeleteModal()" class="w-full py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider cursor-pointer transition">
                    Cancel
                </button>
                <button type="submit" class="w-full py-3.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-rose-950/20 cursor-pointer transition">
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openDeleteModal(id, name, role) {
        document.getElementById('delete_user_name_display').innerText = name;
        document.getElementById('delete_user_role_display').innerText = role;
        document.getElementById('delete_user_form').action = `/admin/users/${id}`;
        let modal = document.getElementById('delete_confirm_modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        let modal = document.getElementById('delete_confirm_modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endpush