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
                <p class="text-xs text-slate-500 font-bold mt-0.5">Manage institutional student, faculty, and administrator directory records</p>
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

                <!-- Dropdown Options Menu -->
                <div id="export_dropdown_menu" class="hidden absolute right-0 mt-2.5 w-72 bg-white border-2 border-slate-200 rounded-2xl shadow-2xl z-50 py-2 overflow-hidden animate-in fade-in zoom-in-95">
                    <div class="px-4 py-2 border-b border-slate-100 text-[10px] uppercase font-black tracking-wider text-slate-400 bg-slate-50/50">
                        Select Directory Export
                    </div>
                    
                    @if(Route::has('admin.users.export'))
                        <a href="{{ route('admin.users.export', ['type' => 'students']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-red-50 hover:text-[#8b1818] transition">
                            <div class="w-8 h-8 rounded-xl bg-red-100 text-[#8b1818] flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div>
                                <p class="font-extrabold leading-snug">Students Directory</p>
                                <span class="text-[10px] text-slate-400 font-medium">Export enrolled students</span>
                            </div>
                        </a>

                        <a href="{{ route('admin.users.export', ['type' => 'faculty']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-900 transition">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <div>
                                <p class="font-extrabold leading-snug">Faculty Members</p>
                                <span class="text-[10px] text-slate-400 font-medium">Export teaching staff</span>
                            </div>
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>

                        <a href="{{ route('admin.users.export', ['type' => 'all']) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <p class="font-extrabold leading-snug">All Users Combined</p>
                                <span class="text-[10px] text-slate-400 font-medium">Excluding system admin</span>
                            </div>
                        </a>
                    @else
                        <div class="px-4 py-3 text-xs text-slate-400 italic">Export route unavailable</div>
                    @endif
                </div>
            </div>

            <!-- Add New User CTA -->
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
            <!-- Filter Tabs -->
            <div class="inline-flex p-1.5 bg-slate-200/70 rounded-2xl border-2 border-slate-200 text-xs font-black gap-1 shadow-2xs">
                <a href="{{ route('admin.users.index', ['role' => 'all', 'search' => $search]) }}" 
                   class="px-4 py-2.5 rounded-xl transition {{ $roleFilter === 'all' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">
                    All Users <span class="ml-1 text-[11px] opacity-75 font-bold">({{ $totalUsers }})</span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'student', 'search' => $search]) }}" 
                   class="px-4 py-2.5 rounded-xl transition {{ $roleFilter === 'student' ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">
                    Students <span class="ml-1 text-[11px] opacity-75 font-bold">({{ $studentCount }})</span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'faculty', 'search' => $search]) }}" 
                   class="px-4 py-2.5 rounded-xl transition {{ in_array($roleFilter, ['faculty', 'teacher']) ? 'bg-white text-[#8b1818] shadow-sm font-black' : 'text-slate-600 hover:text-[#8b1818]' }}">
                    Faculty <span class="ml-1 text-[11px] opacity-75 font-bold">({{ $teacherCount }})</span>
                </a>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 w-full md:w-96">
                <input type="hidden" name="role" value="{{ $roleFilter }}">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name, ID, or email..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-white transition shadow-2xs">
                </div>
                @if(!empty($search))
                    <a href="{{ route('admin.users.index', ['role' => $roleFilter]) }}" 
                       class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-black transition shrink-0 shadow-2xs">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- ================= SECTION 1: STUDENTS DIRECTORY ================= -->
        @if($roleFilter === 'all' || $roleFilter === 'student')
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex items-center justify-between border-b-2 border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-red-100 text-[#8b1818] flex items-center justify-center font-black text-lg shadow-2xs shrink-0">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Enrolled Students</h2>
                        <p class="text-xs text-slate-500 font-bold mt-0.5">Active enrolled student directory</p>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-black text-[#8b1818] bg-red-50 px-3.5 py-1.5 rounded-full border border-red-200 shadow-2xs">
                        Total Students: {{ $students->total() }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-6">Photo</th>
                            <th class="py-4 px-6">LRN / School ID</th>
                            <th class="py-4 px-6">Student Name</th>
                            <th class="py-4 px-6 text-center">Gender</th>
                            <th class="py-4 px-6 text-center">Academic Placement</th>
                            <th class="py-4 px-6 text-center">NFC Card UID</th>
                            <th class="py-4 px-6">Parent Contact</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($students as $student)
                            <tr class="hover:bg-red-50/30 transition">
                                <!-- Student Photo -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        @if(!empty($student->photo))
                                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->first_name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-xs">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-red-100 border border-red-200 text-[#8b1818] text-xs font-black flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? 'T', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- LRN -->
                                <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">
                                    {{ $student->id_number ?? 'Not Set' }}
                                </td>

                                <!-- Student Name & Email -->
                                <td class="py-4 px-6">
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-sm leading-tight">
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </div>
                                        <span class="block text-xs text-slate-400 font-medium">{{ $student->email }}</span>
                                    </div>
                                </td>

                                <!-- Gender Badge -->
                                <td class="py-4 px-6 text-center">
                                    @if((int)$student->gender === 1)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-mars text-[10px]"></i> Male
                                        </span>
                                    @elseif((int)$student->gender === 2)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-venus text-[10px]"></i> Female
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">N/A</span>
                                    @endif
                                </td>

                                <!-- Academic Placement -->
                                <td class="py-4 px-6 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-slate-100 text-slate-800 border border-slate-200 shadow-2xs">
                                            {{ $student->grade_level ?? 'Grade Level' }}
                                        </span>
                                        <div class="text-[11px] font-bold text-amber-700">
                                            {{ $student->strand ?? 'Strand' }} @if(!empty($student->section)) • Section {{ $student->section }} @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- NFC Tag UID -->
                                <td class="py-4 px-6 text-center font-mono font-bold text-xs">
                                    @if($student->nfcCard && !empty($student->nfcCard->tag_id))
                                        <span class="text-[#8b1818] bg-red-50 px-3 py-1 rounded-xl border border-red-200 shadow-2xs inline-block">
                                            <i class="fa-solid fa-nfc-symbol mr-1 text-[11px]"></i>
                                            {{ $student->nfcCard->tag_id }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-sans italic text-xs">No Card</span>
                                    @endif
                                </td>

                                <!-- Parent Contact Info -->
                                <td class="py-4 px-6 text-slate-700 text-xs">
                                    <div class="font-extrabold text-slate-900">
                                        {{ $student->parent_phone_number ?? 'N/A' }}
                                    </div>
                                    @if(!empty($student->parent_name))
                                        <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                            <i class="fa-solid fa-user-group text-[9px] mr-1"></i>{{ $student->parent_name }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $student->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-slate-900 text-xs font-bold border border-slate-200 shadow-xs transition-all duration-150 active:scale-[0.98]">
                                            <i class="fa-solid fa-pen-to-square text-[11px] text-slate-500"></i>
                                            <span>Edit</span>
                                        </a>

                                        <button type="button" 
                                                onclick="openDeleteModal('{{ $student->id }}', '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', 'Student')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] text-xs font-bold border border-red-200/80 shadow-xs transition-all duration-150 active:scale-[0.98] cursor-pointer">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-16 text-center text-slate-400 font-medium">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3.5 text-xl shadow-2xs">
                                        <i class="fa-solid fa-user-slash"></i>
                                    </div>
                                    <p class="text-base font-extrabold text-slate-800">No Student Accounts Found</p>
                                    <p class="text-xs text-slate-500 font-semibold mt-1">Try adjusting your search criteria or register a new student.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($students, 'links') && $students->hasPages())
                <div class="p-5 border-t-2 border-slate-100 bg-slate-50/50">
                    {{ $students->appends(['role' => $roleFilter, 'search' => $search])->links() }}
                </div>
            @endif
        </div>
        @endif

        <!-- ================= SECTION 2: FACULTY DIRECTORY ================= -->
        @if($roleFilter === 'all' || in_array($roleFilter, ['faculty', 'teacher']))
        <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-sm overflow-hidden space-y-0 w-full">
            <div class="p-6 lg:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-slate-100 bg-white">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-lg shadow-2xs shrink-0">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Faculty Members</h2>
                        <p class="text-xs text-slate-500 font-bold mt-0.5">Teaching personnel and staff directory</p>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-black text-amber-900 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-300 shadow-2xs">
                        Total Faculty: {{ $faculty->total() }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/90 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-6">Photo</th>
                            <th class="py-4 px-6">Faculty ID</th>
                            <th class="py-4 px-6">Faculty Name</th>
                            <th class="py-4 px-6 text-center">Gender</th>
                            <th class="py-4 px-6 text-center">Position</th>
                            <th class="py-4 px-6">Contact Number</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($faculty as $member)
                            <tr class="hover:bg-amber-50/30 transition">
                                <!-- Faculty Photo -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        @if(!empty($member->photo))
                                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->first_name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-xs">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-amber-100 border border-amber-300 text-amber-900 text-xs font-black flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($member->first_name ?? 'F', 0, 1)) }}{{ strtoupper(substr($member->last_name ?? 'T', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Faculty ID -->
                                <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">
                                    {{ $member->id_number ?? 'N/A' }}
                                </td>

                                <!-- Faculty Name & Email -->
                                <td class="py-4 px-6">
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-sm leading-tight">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </div>
                                        <span class="block text-xs text-slate-400 font-medium">{{ $member->email }}</span>
                                    </div>
                                </td>

                                <!-- Gender Badge -->
                                <td class="py-4 px-6 text-center">
                                    @if((int)$member->gender === 1)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            <i class="fa-solid fa-mars text-[10px]"></i> Male
                                        </span>
                                    @elseif((int)$member->gender === 2)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-venus text-[10px]"></i> Female
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">N/A</span>
                                    @endif
                                </td>

                                <!-- Position Badge -->
                                <td class="py-4 px-6 text-center">
                                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs">A.Y. {{ $activeSchoolYear ?? '2027-2028' }}</span>
                                </td>

                                <!-- Contact Number -->
                                <td class="py-4 px-6 text-slate-700 text-sm font-bold">
                                    {{ $member->phone_number ?? 'N/A' }}
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Active
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-3.5 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.users.edit', $member->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-slate-900 text-xs font-bold border border-slate-200 shadow-xs transition-all duration-150 active:scale-[0.98]">
                                            <i class="fa-solid fa-pen-to-square text-[11px] text-slate-500"></i>
                                            <span>Edit</span>
                                        </a>

                                        <button type="button" 
                                                onclick="openDeleteModal('{{ $member->id }}', '{{ addslashes($member->first_name . ' ' . $member->last_name) }}', 'Faculty Member')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-[#8b1818] text-xs font-bold border border-red-200/80 shadow-xs transition-all duration-150 active:scale-[0.98] cursor-pointer">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center text-slate-400 font-medium">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 border-2 border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3.5 text-xl shadow-2xs">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </div>
                                    <p class="text-base font-extrabold text-slate-800">No Faculty Accounts Found</p>
                                    <p class="text-xs text-slate-500 font-semibold mt-1">Try adjusting your search query or register a new teacher.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($faculty, 'links') && $faculty->hasPages())
                <div class="p-5 border-t-2 border-slate-100 bg-slate-50/50">
                    {{ $faculty->appends(['role' => $roleFilter, 'search' => $search])->links() }}
                </div>
            @endif
        </div>
        @endif  

    </main>
</div>

<!-- ================= SUCCESS DIALOG POPUP ================= -->
@if(session('success') || session('new_user_created'))
@php 
    $newUser = session('new_user_created');
    $successMessage = session('success') ?? 'Action completed successfully!';
@endphp

<div id="sia_success_dialog" 
     class="fixed inset-0 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     style="z-index: 99999;">
    
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border-2 border-slate-100 overflow-hidden transform transition-all p-6 sm:p-8 text-center space-y-5 animate-in fade-in zoom-in-95">
        
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl border-2 border-emerald-200 shadow-inner">
            <i class="fa-solid fa-check"></i>
        </div>

        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Success!</h3>
            <p class="text-sm font-bold text-slate-600 mt-1.5 leading-relaxed">
                {{ $successMessage }}
            </p>
        </div>

        @if($newUser)
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left space-y-2 text-xs font-semibold text-slate-700">
            <div class="flex justify-between pb-1.5 border-b border-slate-200">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Name</span>
                <span class="font-extrabold text-slate-900">{{ $newUser['name'] ?? '' }}</span>
            </div>
            <div class="flex justify-between pb-1.5 border-b border-slate-200">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Role</span>
                <span class="font-bold text-[#8b1818]">{{ $newUser['role'] ?? '' }}</span>
            </div>
            <div class="flex justify-between pb-1.5 border-b border-slate-200">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">ID / LRN</span>
                <span class="font-mono font-bold">{{ $newUser['id_number'] ?? '' }}</span>
            </div>
            @if(!empty($newUser['password']))
            <div class="flex justify-between items-center pb-1.5 border-b border-slate-200">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Password</span>
                <span class="font-mono font-bold text-[#8b1818] bg-red-50 px-2 py-0.5 rounded border border-red-200">{{ $newUser['password'] }}</span>
            </div>
            @endif
            @if(!empty($newUser['nfc_tag']))
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">NFC Tag</span>
                <span class="font-mono font-bold text-[#8b1818] bg-red-50 px-2 py-0.5 rounded border border-red-200">{{ $newUser['nfc_tag'] }}</span>
            </div>
            @endif
        </div>
        @endif

        <div class="pt-2">
            <button type="button" 
                    onclick="closeSiaDialog()" 
                    class="w-full py-3.5 px-6 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-red-950/20 active:scale-[0.98] cursor-pointer">
                Okay, Continue
            </button>
        </div>
    </div>
</div>
@endif

<!-- ================= LARGE DELETE CONFIRMATION MODAL ================= -->
<div id="delete_confirm_modal" 
     class="hidden fixed inset-0 items-center justify-center p-4 sm:p-6 bg-slate-950/60 transition-all duration-200"
     style="z-index: 99999; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;">
    
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
            
            <div class="p-4 bg-red-50/80 rounded-2xl border border-red-200 text-xs text-red-700 font-bold max-w-md mx-auto flex items-center justify-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-sm shrink-0"></i>
                <span>This action is permanent and will unbind all linked NFC credentials.</span>
            </div>
        </div>

        <form id="delete_user_form" method="POST" action="" class="pt-2">
            @csrf
            @method('DELETE')
            
            <div class="grid grid-cols-2 gap-4">
                <button type="button" 
                        onclick="closeDeleteModal()" 
                        class="w-full py-4 px-6 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase tracking-wider transition border border-slate-200 cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="w-full py-4 px-6 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-red-950/20 active:scale-[0.98] border-b-4 border-[#5e0f0f] cursor-pointer">
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
        const menu = document.getElementById('export_dropdown_menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function closeSiaDialog() {
        const dialog = document.getElementById('sia_success_dialog');
        if (dialog) {
            dialog.style.opacity = '0';
            dialog.style.transition = 'opacity 0.2s ease';
            setTimeout(() => dialog.remove(), 200);
        }
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('export_dropdown_menu');
        const btn = e.target.closest('button');
        if (menu && !menu.classList.contains('hidden')) {
            if (!btn || !btn.getAttribute('onclick')?.includes('toggleExportMenu')) {
                if (!menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            }
        }
    });

    function openDeleteModal(userId, userName, userRole) {
        const modal = document.getElementById('delete_confirm_modal');
        const nameDisplay = document.getElementById('delete_user_name_display');
        const roleDisplay = document.getElementById('delete_user_role_display');
        const form = document.getElementById('delete_user_form');

        if (modal && nameDisplay && form) {
            nameDisplay.innerText = userName;
            if (roleDisplay) roleDisplay.innerText = userRole;
            form.action = `/admin/users/${userId}`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete_confirm_modal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }
</script>
@endpush