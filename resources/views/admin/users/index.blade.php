@extends('layouts.app')

@section('title', 'User Management - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-[#8b1818] text-white flex items-center justify-center text-lg shadow-md shadow-red-950/20 shrink-0">
                <i class="fa-solid fa-users text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">User Management</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Manage institutional student, faculty, director, and administrator directory records</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Export CSV Dropdown -->
            <div class="relative">
                <button type="button" 
                        onclick="toggleExportMenu()" 
                        class="inline-flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-black uppercase tracking-wider border-2 border-slate-200 shadow-2xs hover:border-slate-300 transition cursor-pointer">
                    <i class="fa-solid fa-file-csv text-emerald-600 text-base"></i> 
                    <span>Export CSV</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                </button>

                <div id="export_dropdown_menu" class="hidden absolute right-0 mt-2.5 w-72 bg-white border-2 border-slate-200 rounded-2xl shadow-2xl z-50 py-2 overflow-hidden animate-in fade-in zoom-in-95">
                    <div class="px-4 py-2 border-b border-slate-100 text-[10px] uppercase font-black tracking-wider text-slate-400 bg-slate-50/50">
                        Select Directory Export
                    </div>
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

            <!-- Add New User Link (Routes directly to your separate create blade view) -->
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/20 hover:shadow-xl transition-all duration-150 active:scale-[0.98] border-b-4 border-[#5e0f0f] shrink-0">
                <i class="fa-solid fa-user-plus text-sm text-amber-300"></i>
                <span>Add New User</span>
            </a>
        </div> 
    </header>

    <!-- Main Content Body -->
    <main class="pt-8 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-8 flex-1">
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
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, ID, or email..." class="w-full pl-9 pr-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-white transition shadow-2xs">
                </div>
                @if(!empty($search))
                    <a href="{{ route('admin.users.index', ['role' => $roleFilter]) }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-black transition shrink-0 shadow-2xs">Clear</a>
                @endif
            </form>
        </div>

        <!-- ================= SECTION: ADMINISTRATORS ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'admin')
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b-2 border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-shield-halved"></i></div>
                    <div><h2 class="text-lg font-black text-slate-900 tracking-tight">Administrators</h2><p class="text-xs text-slate-500 font-bold mt-0.5">System administration accounts</p></div>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300">Total Admins: {{ isset($admins) ? $admins->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-6">Name</th><th class="py-4 px-6">Email</th><th class="py-4 px-6 text-center">Status</th><th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($admins ?? [] as $adminUser)
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6 font-extrabold text-slate-900">{{ $adminUser->first_name ?? '' }} {{ $adminUser->last_name ?? $adminUser->name }}</td>
                            <td class="py-4 px-6 text-slate-600">{{ $adminUser->email }}</td>
                            <td class="py-4 px-6 text-center"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active</span></td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $adminUser->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200"><i class="fa-solid fa-pen-to-square text-[11px] text-slate-500"></i> Edit</a>
                                    @if(auth()->id() !== $adminUser->id)
                                    <button type="button" onclick="openDeleteModal('{{ $adminUser->id }}', '{{ addslashes(($adminUser->first_name ?? '') . ' ' . ($adminUser->last_name ?? $adminUser->name)) }}', 'Administrator')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] text-xs font-bold border border-red-200 cursor-pointer"><i class="fa-solid fa-trash text-[11px]"></i> Delete</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-slate-400 font-medium">No Administrator accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- ================= SECTION: DIRECTORS ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'director')
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b-2 border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-user-tie"></i></div>
                    <div><h2 class="text-lg font-black text-slate-900 tracking-tight">Directors</h2><p class="text-xs text-slate-500 font-bold mt-0.5">Director personnel directory</p></div>
                </div>
                <span class="text-xs font-black text-purple-900 bg-purple-50 px-3.5 py-1.5 rounded-full border border-purple-300">Total Directors: {{ isset($directors) ? $directors->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-6">Name</th><th class="py-4 px-6">Email</th><th class="py-4 px-6 text-center">Status</th><th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($directors ?? [] as $directorUser)
                        <tr class="hover:bg-purple-50/30 transition">
                            <td class="py-4 px-6 font-extrabold text-slate-900">{{ $directorUser->first_name ?? '' }} {{ $directorUser->last_name ?? $directorUser->name }}</td>
                            <td class="py-4 px-6 text-slate-600">{{ $directorUser->email }}</td>
                            <td class="py-4 px-6 text-center"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active</span></td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $directorUser->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200"><i class="fa-solid fa-pen-to-square text-[11px] text-slate-500"></i> Edit</a>
                                    <button type="button" onclick="openDeleteModal('{{ $directorUser->id }}', '{{ addslashes(($directorUser->first_name ?? '') . ' ' . ($directorUser->last_name ?? $directorUser->name)) }}', 'Director')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] text-xs font-bold border border-red-200 cursor-pointer"><i class="fa-solid fa-trash text-[11px]"></i> Delete</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-12 text-center text-slate-400 font-medium">No Director accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- ================= SECTION: FACULTY DIRECTORY ================= -->
        @if($roleFilter === 'all' || in_array($roleFilter, ['faculty', 'teacher']))
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b-2 border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div><h2 class="text-lg font-black text-slate-900 tracking-tight">Faculty Members</h2><p class="text-xs text-slate-500 font-bold mt-0.5">Teaching personnel and staff directory</p></div>
                </div>
                <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300">Total Faculty: {{ isset($faculty) ? $faculty->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-6">Photo</th><th class="py-4 px-6">Faculty ID</th><th class="py-4 px-6">Faculty Name</th><th class="py-4 px-6 text-center">Gender</th><th class="py-4 px-6">Contact Number</th><th class="py-4 px-6 text-center">Status</th><th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($faculty ?? [] as $member)
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6">
                                @if(!empty($member->photo))
                                    <img src="{{ asset('storage/' . $member->photo) }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-amber-100 border border-amber-300 text-amber-900 text-xs font-black flex items-center justify-center">{{ strtoupper(substr($member->first_name ?? 'F', 0, 1)) }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">{{ $member->id_number ?? 'N/A' }}</td>
                            <td class="py-4 px-6"><div class="font-extrabold text-slate-900 text-sm">{{ $member->first_name }} {{ $member->last_name }}</div><span class="text-xs text-slate-400">{{ $member->email }}</span></td>
                            <td class="py-4 px-6 text-center">{{ (int)$member->gender === 1 ? 'Male' : 'Female' }}</td>
                            <td class="py-4 px-6 text-slate-700 text-sm font-bold">{{ $member->phone_number ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-center"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active</span></td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200"><i class="fa-solid fa-pen-to-square text-[11px] text-slate-500"></i> Edit</a>
                                    <button type="button" onclick="openDeleteModal('{{ $member->id }}', '{{ addslashes($member->first_name . ' ' . $member->last_name) }}', 'Faculty Member')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] text-xs font-bold border border-red-200 cursor-pointer"><i class="fa-solid fa-trash text-[11px]"></i> Delete</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-12 text-center text-slate-400 font-medium">No Faculty accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- ================= SECTION: STUDENTS DIRECTORY ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'student')
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b-2 border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-red-100 text-[#8b1818] flex items-center justify-center font-black text-lg shadow-2xs shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div><h2 class="text-lg font-black text-slate-900 tracking-tight">Enrolled Students</h2><p class="text-xs text-slate-500 font-bold mt-0.5">Active enrolled student directory</p></div>
                </div>
                <span class="text-xs font-black text-[#8b1818] bg-red-50 px-3.5 py-1.5 rounded-full border border-red-200">Total Students: {{ isset($students) ? $students->total() : 0 }}</span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-6">Photo</th><th class="py-4 px-6">LRN</th><th class="py-4 px-6">Student Name</th><th class="py-4 px-6 text-center">Placement</th><th class="py-4 px-6 text-center">NFC Card</th><th class="py-4 px-6">Parent Contact</th><th class="py-4 px-6 text-center">Status</th><th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($students ?? [] as $student)
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="py-4 px-6">
                                @if(!empty($student->photo))
                                    <img src="{{ asset('storage/' . $student->photo) }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-red-100 border border-red-200 text-[#8b1818] text-xs font-black flex items-center justify-center">{{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">{{ $student->id_number ?? 'Not Set' }}</td>
                            <td class="py-4 px-6"><div class="font-extrabold text-slate-900 text-sm">{{ $student->first_name }} {{ $student->last_name }}</div><span class="text-xs text-slate-400">{{ $student->email }}</span></td>
                            <td class="py-4 px-6 text-center"><span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-slate-100 text-slate-800">Grade {{ $student->grade_level ?? '--' }}</span></td>
                            <td class="py-4 px-6 text-center font-mono font-bold text-xs">{{ optional($student->nfcCard)->tag_id ?? 'No Card' }}</td>
                            <td class="py-4 px-6 text-slate-700 text-xs"><div class="font-extrabold text-slate-900">{{ $student->parent_phone_number ?? 'N/A' }}</div></td>
                            <td class="py-4 px-6 text-center"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active</span></td>
                            <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $student->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200"><i class="fa-solid fa-pen-to-square text-[11px] text-slate-500"></i> Edit</a>
                                    <button type="button" onclick="openDeleteModal('{{ $student->id }}', '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', 'Student')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] text-xs font-bold border border-red-200 cursor-pointer"><i class="fa-solid fa-trash text-[11px]"></i> Delete</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="py-12 text-center text-slate-400 font-medium">No Student accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </main>
</div>

<!-- ================= LARGE DELETE CONFIRMATION MODAL ================= -->
<div id="delete_confirm_modal" class="hidden fixed inset-0 items-center justify-center p-4 sm:p-6 bg-slate-950/60 transition-all duration-200" style="z-index: 99999; backdrop-filter: blur(14px) !important;">
    <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border-2 border-red-100 overflow-hidden transform transition-all p-8 sm:p-10 text-center space-y-6 animate-in fade-in zoom-in-95">
        <div class="w-20 h-20 bg-red-50 text-[#8b1818] rounded-3xl flex items-center justify-center mx-auto text-3xl border-2 border-red-200 shadow-sm">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <div class="space-y-3">
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Confirm Account Deletion</h3>
            <p class="text-sm font-semibold text-slate-600 leading-relaxed max-w-md mx-auto">
                Are you sure you want to permanently remove <br class="hidden sm:inline">
                <span id="delete_user_name_display" class="font-extrabold text-slate-900 text-base"></span> 
                (<span id="delete_user_role_display" class="font-bold text-[#8b1818]"></span>)?
            </p>
        </div>
        <form id="delete_user_form" method="POST" action="" class="pt-2">
            @csrf
            @method('DELETE')
            <div class="grid grid-cols-2 gap-4">
                <button type="button" onclick="closeDeleteModal()" class="w-full py-4 px-6 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase tracking-wider transition border border-slate-200 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-red-950/25 active:scale-[0.98] border-b-4 border-[#5e0f0f] cursor-pointer">
                    Yes, Delete Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleExportMenu() {
        document.getElementById('export_dropdown_menu').classList.toggle('hidden');
    }
    function openDeleteModal(id, name, role) {
        document.getElementById('delete_user_name_display').innerText = name;
        document.getElementById('delete_user_role_display').innerText = role;
        document.getElementById('delete_user_form').action = `/admin/users/${id}`;
        document.getElementById('delete_confirm_modal').classList.remove('hidden');
        document.getElementById('delete_confirm_modal').classList.add('flex');
    }
    function closeDeleteModal() {
        document.getElementById('delete_confirm_modal').classList.remove('flex');
        document.getElementById('delete_confirm_modal').classList.add('hidden');
    }
</script>
@endpush