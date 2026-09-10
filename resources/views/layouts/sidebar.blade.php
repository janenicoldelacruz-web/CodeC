<aside class="w-72 bg-white border-r-2 border-slate-200 flex flex-col justify-between shrink-0 h-screen fixed top-0 left-0 z-30 shadow-xl shadow-slate-900/5">
    
    <!-- Top Accent Trim -->
    <div class="h-1.5 bg-gradient-to-r from-[#8b1818] via-amber-400 to-[#8b1818] w-full shrink-0"></div>

    <div class="flex-1 flex flex-col min-h-0">
        <!-- Brand Header -->
        <div class="p-5 border-b-2 border-slate-100 flex items-center gap-3.5 bg-slate-50/60">
            <div class="w-11 h-11 rounded-xl bg-white p-1 border-2 border-[#8b1818] ring-2 ring-amber-300/60 shadow-xs flex items-center justify-center shrink-0 overflow-hidden">
                <img src="{{ asset('images/sia-logo.png') }}" alt="SIA Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-black tracking-tight text-[#8b1818]">SIATRACK</span>
                    
                    {{-- Dynamic Role Badge --}}
                    @if(auth()->user()->role_id === 1)
                        <span class="px-2 py-0.5 rounded bg-amber-100 border border-amber-300 text-[10px] font-black text-amber-900 uppercase">A.Y. {{ $activeSchoolYear ?? '2027-2028' }}</span>
                    @elseif(auth()->user()->role_id === 2)
                        <span class="px-2 py-0.5 rounded bg-red-100 border border-red-300 text-[10px] font-black text-[#8b1818] uppercase">Teacher</span>
                    @else
                        <span class="px-2 py-0.5 rounded bg-blue-100 border border-blue-300 text-[10px] font-black text-blue-900 uppercase">Student</span>
                    @endif
                </div>
                <p class="text-xs font-bold text-slate-500 truncate mt-0.5">Southern Isabela Academy</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-6 overflow-y-auto flex-1 custom-scrollbar">
            
            {{-- ==================== ADMIN NAVIGATION ==================== --}}
            @if(auth()->user()->role_id === 1)
                <div>
                    <p class="px-3.5 text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Management Menu</p>
                    <div class="space-y-1.5 text-sm font-bold">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-table-cells-large text-base {{ request()->routeIs('admin.dashboard') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Dashboard</span>
                            </div>
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-users text-base {{ request()->routeIs('admin.users.*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">User Management</span>
                            </div>
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-3.5 text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Modules</p>
                    <div class="space-y-1.5 text-sm font-bold">
                        <!-- Class Schedule -->
                        <a href="{{ route('admin.schedules') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.schedules*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-calendar-days text-base {{ request()->routeIs('admin.schedules*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Class Schedule</span>
                            </div>
                        </a>

                        <!-- Attendance (Kiosk Attendance) -->
                        <a href="{{ route('admin.attendance') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.attendance*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-id-card-clip text-base {{ request()->routeIs('admin.attendance*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Attendance (Kiosk)</span>
                            </div>
                        </a>

                        <!-- Evaluation -->
                        <a href="{{ route('admin.evaluations') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.evaluations*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-star-half-stroke text-base {{ request()->routeIs('admin.evaluations*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Evaluation</span>
                            </div>
                        </a>

                        <!-- Report -->
                        <a href="{{ route('admin.reports') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.reports*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-file-invoice text-base {{ request()->routeIs('admin.reports*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Report</span>
                            </div>
                        </a>
            <a href="{{ route('admin.school-year') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('admin.school-year*') ? 'bg-[#8b1818] text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">
                <i class="fa-solid fa-calendar-check text-sm w-4"></i>
                <span>School Year</span>
            </a>
                    </div>
                </div>

           {{-- ==================== TEACHER NAVIGATION ==================== --}}
@elseif(auth()->user()->role_id === 2)
    <div>
        <p class="px-3.5 text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Faculty Portal</p>
        <div class="space-y-1.5 text-sm font-bold">
            
            <!-- Class Schedule (Now the primary landing page) -->
            <a href="{{ route('teacher.schedules') }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('teacher.schedules*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                <div class="flex items-center gap-3.5">
                    <i class="fa-solid fa-calendar-days text-base {{ request()->routeIs('teacher.schedules*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                    <span class="text-[14px]">Class Schedule</span>
                </div>
            </a>

            <!-- Attendance (Kiosk Attendance Feed) -->
<a href="{{ route('teacher.attendance') }}" 
   class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('teacher.attendance*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
    <div class="flex items-center gap-3.5">
        <i class="fa-solid fa-clipboard-user text-base {{ request()->routeIs('teacher.attendance*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
        <span class="text-[14px]">Attendance</span>
    </div>
</a>

            <!-- Evaluation -->
            <a href="{{ route('teacher.evaluation.report') }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('teacher.evaluation*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                <div class="flex items-center gap-3.5">
                    <i class="fa-solid fa-star-half-stroke text-base {{ request()->routeIs('teacher.evaluation*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                    <span class="text-[14px]">Evaluation</span>
                </div>
            </a>

            <!-- Report -->
            <a href="{{ route('teacher.evaluation.report') }}" 
               class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('teacher.report*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                <div class="flex items-center gap-3.5">
                    <i class="fa-solid fa-file-invoice text-base {{ request()->routeIs('teacher.report*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                    <span class="text-[14px]">Report</span>
                </div>
            </a>
        </div>
    </div>
    
            {{-- ==================== STUDENT NAVIGATION ==================== --}}
            @elseif(auth()->user()->role_id === 3)
                <div>
                    <p class="px-3.5 text-xs font-black uppercase tracking-wider text-slate-400 mb-2">Student Portal</p>
                    <div class="space-y-1.5 text-sm font-bold">
                        <a href="{{ route('student.dashboard') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('student.dashboard') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-gauge text-base {{ request()->routeIs('student.dashboard') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">My Dashboard</span>
                            </div>
                        </a>
                        <a href="{{ route('student.attendance') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('student.attendance*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-calendar-check text-base {{ request()->routeIs('student.attendance*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Attendance Records</span>
                            </div>
                        </a>
                        <a href="{{ route('student.evaluations') }}" 
                           class="group flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('student.evaluations*') ? 'bg-[#8b1818] text-white shadow-md shadow-red-950/20' : 'text-slate-700 hover:bg-red-50 hover:text-[#8b1818]' }}">
                            <div class="flex items-center gap-3.5">
                                <i class="fa-solid fa-star-half-stroke text-base {{ request()->routeIs('student.evaluations*') ? 'text-amber-300' : 'text-slate-400 group-hover:text-[#8b1818]' }}"></i>
                                <span class="text-[14px]">Evaluation</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

        </nav>
    </div>

    <!-- Bottom Logout -->
    <div class="p-4 border-t-2 border-slate-100 bg-slate-50/80">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl text-sm font-extrabold text-slate-700 hover:text-white hover:bg-[#8b1818] bg-white border-2 border-slate-200 hover:border-[#8b1818] shadow-2xs hover:shadow-md transition-all duration-150 active:scale-[0.99] group cursor-pointer">
                <i class="fa-solid fa-right-from-bracket text-base text-slate-400 group-hover:text-amber-300 transition"></i>
                <span class="text-sm">Sign Out</span>
            </button>
        </form>
    </div>

</aside>