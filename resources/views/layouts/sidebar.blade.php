<aside x-data="{ collapsed: false }" 
       @toggle-sidebar.window="collapsed = !collapsed"
       :class="collapsed ? 'w-20' : 'w-72'" 
       class="bg-[#590d0d] border-r border-red-950/40 flex flex-col justify-between shrink-0 h-screen fixed top-0 left-0 z-30 shadow-2xl transition-all duration-300">
    
    <!-- Top Accent Trim -->
    <div class="h-1.5 bg-gradient-to-r from-amber-400 via-amber-200 to-amber-400 w-full shrink-0"></div>

    <div class="flex-1 flex flex-col min-h-0">
<!-- Brand Header & Hamburger Toggle -->
        <div class="p-4 border-b border-red-900/40 flex items-center justify-between bg-black/15 overflow-hidden">
            <div class="flex items-center gap-3 min-w-0" x-show="!collapsed" x-transition>
                <div class="w-10 h-10 rounded-xl bg-white p-1 border-2 border-amber-300 shadow-xs flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('images/sia-logo.png') }}" alt="SIA Logo" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base font-black tracking-tight text-white">SIATRACK</span>
                    </div>
                    <p class="text-[11px] font-bold text-red-200/70 truncate">Southern Isabela Academy</p>
                </div>
            </div>

            <!-- Minimized Logo View -->
            <div class="w-10 h-10 rounded-xl bg-white p-1 border-2 border-amber-300 flex items-center justify-center shrink-0 mx-auto" x-show="collapsed" x-transition>
                <img src="{{ asset('images/sia-logo.png') }}" alt="SIA Logo" class="w-full h-full object-contain">
            </div>

            <!-- Hamburger Toggle Button (Kept safely inside flow) -->
            <button @click="collapsed = !collapsed; window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: collapsed }))" 
                    class="p-2 rounded-xl bg-red-950/60 hover:bg-red-950 text-amber-300 hover:text-white transition cursor-pointer shrink-0 border border-red-900/60 ml-2"
                    title="Toggle Sidebar">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
        </div>
        <!-- Navigation Links -->
        <nav class="p-3 space-y-5 overflow-y-auto flex-1 custom-scrollbar">
            
            {{-- ==================== ADMIN NAVIGATION ==================== --}}
            @if(auth()->user()->role_id === 1)
                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-red-200/60 mb-2" x-show="!collapsed">Management Menu</p>
                    <div class="space-y-1 text-sm font-bold">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-400 text-amber-950 shadow-md' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}"
                           title="Dashboard">
                            <i class="fa-solid fa-table-cells-large text-base {{ request()->routeIs('admin.dashboard') ? 'text-amber-950' : 'text-amber-300 group-hover:text-white' }}"></i>
                            <span class="text-[13px]" x-show="!collapsed">Dashboard</span>
                        </a>

                        <!-- User Management with Sub-items -->
                        <div x-data="{ open: @json(request()->routeIs('admin.users*')) }" class="space-y-1">
                            <button @click="open = !open" 
                                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.users*') ? 'bg-red-900/80 text-amber-300' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}">
                                <div class="flex items-center gap-3.5">
                                    <i class="fa-solid fa-users text-base {{ request()->routeIs('admin.users*') ? 'text-amber-300' : 'text-amber-300 group-hover:text-white' }}"></i>
                                    <span class="text-[13px]" x-show="!collapsed">User Management</span>
                                </div>
                            </button>
                            <div x-show="open && !collapsed" x-cloak class="pl-11 pr-2 space-y-1 py-1">
                                <a href="{{ route('admin.users.index', ['role' => 'administrator']) }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Administrators</a>
                                <a href="{{ route('admin.users.index', ['role' => 'director']) }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Directors</a>
                                <a href="{{ route('admin.users.index', ['role' => 'faculty']) }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Faculty</a>
                                <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Students</a>
                            </div>
                        </div>

                        <!-- NFC Management with Sub-items -->
                        <div x-data="{ open: @json(request()->routeIs('admin.nfc*')) }" class="space-y-1">
                            <button @click="open = !open" 
                                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.nfc*') ? 'bg-red-900/80 text-amber-300' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}">
                                <div class="flex items-center gap-3.5">
                                    <i class="fa-solid fa-id-card text-base {{ request()->routeIs('admin.nfc*') ? 'text-amber-300' : 'text-amber-300 group-hover:text-white' }}"></i>
                                    <span class="text-[13px]" x-show="!collapsed">NFC Management</span>
                                </div>
                            </button>
                            <div x-show="open && !collapsed" x-cloak class="pl-11 pr-2 space-y-1 py-1">
                                <a href="{{ route('admin.nfc.binding') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Card Registration & Binding</a>
                                <a href="{{ route('admin.nfc.replacement') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Lost & Replacement Cards</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-red-200/60 mb-2" x-show="!collapsed">Modules & Setup</p>
                    <div class="space-y-1 text-sm font-bold">
                        <!-- Academic Setup with Sub-items -->
                        <div x-data="{ open: @json(request()->routeIs('admin.school-year*', 'admin.sections*', 'admin.schedules*')) }" class="space-y-1">
                            <button @click="open = !open" 
                                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.school-year*', 'admin.sections*', 'admin.schedules*') ? 'bg-red-900/80 text-amber-300' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}">
                                <div class="flex items-center gap-3.5">
                                    <i class="fa-solid fa-graduation-cap text-base {{ request()->routeIs('admin.school-year*', 'admin.sections*', 'admin.schedules*') ? 'text-amber-300' : 'text-amber-300 group-hover:text-white' }}"></i>
                                    <span class="text-[13px]" x-show="!collapsed">Academic Setup</span>
                                </div>
                            </button>
                            <div x-show="open && !collapsed" x-cloak class="pl-11 pr-2 space-y-1 py-1">
                                <a href="{{ route('admin.school-year') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">School Year & Semesters</a>
                                <a href="{{ route('admin.sections') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Section & Class Management</a>
                                <a href="{{ route('admin.schedules') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Class Schedules & Rooms</a>
                            </div>
                        </div>

                        <!-- Attendance Monitoring with Sub-items -->
                        <div x-data="{ open: @json(request()->routeIs('admin.attendance*')) }" class="space-y-1">
                            <button @click="open = !open" 
                                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.attendance*') ? 'bg-red-900/80 text-amber-300' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}">
                                <div class="flex items-center gap-3.5">
                                    <i class="fa-solid fa-id-card-clip text-base {{ request()->routeIs('admin.attendance*') ? 'text-amber-300' : 'text-amber-300 group-hover:text-white' }}"></i>
                                    <span class="text-[13px]" x-show="!collapsed">Attendance Monitoring</span>
                                </div>
                            </button>
                            <div x-show="open && !collapsed" x-cloak class="pl-11 pr-2 space-y-1 py-1">
                                <a href="{{ route('admin.attendance.live') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Live Tap Feed</a>
                                <a href="{{ route('admin.attendance.override') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Manual Attendance Override</a>
                            </div>
                        </div>

                        <!-- Faculty Evaluation with Sub-items -->
                        <div x-data="{ open: @json(request()->routeIs('admin.evaluations*')) }" class="space-y-1">
                            <button @click="open = !open" 
                                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.evaluations*') ? 'bg-red-900/80 text-amber-300' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}">
                                <div class="flex items-center gap-3.5">
                                    <i class="fa-solid fa-star-half-stroke text-base {{ request()->routeIs('admin.evaluations*') ? 'text-amber-300' : 'text-amber-300 group-hover:text-white' }}"></i>
                                    <span class="text-[13px]" x-show="!collapsed">Faculty Evaluation</span>
                                </div>
                            </button>
                            <div x-show="open && !collapsed" x-cloak class="pl-11 pr-2 space-y-1 py-1">
                                <a href="{{ route('admin.evaluations.periods') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Evaluation Periods & Questions</a>
                                <a href="{{ route('admin.evaluations.results') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Results & Ratings Overview</a>
                            </div>
                        </div>

                        <!-- Announcements -->
                        <a href="{{ route('admin.announcements') }}" 
                           class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.announcements*') ? 'bg-amber-400 text-amber-950 shadow-md' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}"
                           title="Announcements">
                            <i class="fa-solid fa-bullhorn text-base {{ request()->routeIs('admin.announcements*') ? 'text-amber-950' : 'text-amber-300 group-hover:text-white' }}"></i>
                            <span class="text-[13px]" x-show="!collapsed">Announcements</span>
                        </a>

                        <!-- Reports with Sub-items -->
                        <div x-data="{ open: @json(request()->routeIs('admin.reports*')) }" class="space-y-1">
                            <button @click="open = !open" 
                                class="w-full group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition duration-150 {{ request()->routeIs('admin.reports*') ? 'bg-red-900/80 text-amber-300' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}">
                                <div class="flex items-center gap-3.5">
                                    <i class="fa-solid fa-file-invoice text-base {{ request()->routeIs('admin.reports*') ? 'text-amber-300' : 'text-amber-300 group-hover:text-white' }}"></i>
                                    <span class="text-[13px]" x-show="!collapsed">Reports</span>
                                </div>
                            </button>
                            <div x-show="open && !collapsed" x-cloak class="pl-11 pr-2 space-y-1 py-1">
                                <a href="{{ route('admin.reports.attendance') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Attendance Logs</a>
                                <a href="{{ route('admin.reports.sf2') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">SF2 (DepEd Compliant)</a>
                                <a href="{{ route('admin.reports.evaluation') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">Faculty Evaluation Summary</a>
                                <a href="{{ route('admin.reports.users') }}" class="block py-1.5 px-3 rounded-lg text-xs font-bold text-red-200/80 hover:text-amber-300 hover:bg-red-900/40 transition">User Master List</a>
                            </div>
                        </div>

                        <!-- System Audit Logs -->
                        <a href="{{ route('admin.audit-logs') }}" 
                           class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.audit-logs*') ? 'bg-amber-400 text-amber-950 shadow-md' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}"
                           title="System Audit Logs">
                            <i class="fa-solid fa-shield-halved text-base {{ request()->routeIs('admin.audit-logs*') ? 'text-amber-950' : 'text-amber-300 group-hover:text-white' }}"></i>
                            <span class="text-[13px]" x-show="!collapsed">System Audit Logs</span>
                        </a>

                        <!-- Settings -->
                        <a href="{{ route('admin.settings') }}" 
                           class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition duration-150 {{ request()->routeIs('admin.settings*') ? 'bg-amber-400 text-amber-950 shadow-md' : 'text-white hover:bg-red-900/50 hover:text-amber-200' }}"
                           title="Settings">
                            <i class="fa-solid fa-gear text-base {{ request()->routeIs('admin.settings*') ? 'text-amber-950' : 'text-amber-300 group-hover:text-white' }}"></i>
                            <span class="text-[13px]" x-show="!collapsed">Settings</span>
                        </a>
                    </div>
                </div>

            {{-- ==================== TEACHER NAVIGATION ==================== --}}
            @elseif(auth()->user()->role_id === 2)
                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-red-200/60 mb-2" x-show="!collapsed">Faculty Portal</p>
                    <div class="space-y-1 text-sm font-bold">
                        <a href="{{ route('teacher.schedules') }}" class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white hover:bg-red-900/50 hover:text-amber-200 transition" title="Class Schedule">
                            <i class="fa-solid fa-calendar-days text-amber-300 group-hover:text-white"></i>
                            <span class="text-[13px]" x-show="!collapsed">Class Schedule</span>
                        </a>
                        <a href="{{ route('teacher.attendance') }}" class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white hover:bg-red-900/50 hover:text-amber-200 transition" title="Attendance">
                            <i class="fa-solid fa-clipboard-user text-amber-300 group-hover:text-white"></i>
                            <span class="text-[13px]" x-show="!collapsed">Attendance</span>
                        </a>
                    </div>
                </div>
            
            {{-- ==================== STUDENT NAVIGATION ==================== --}}
            @elseif(auth()->user()->role_id === 3)
                <div>
                    <p class="px-3 text-[10px] font-black uppercase tracking-wider text-red-200/60 mb-2" x-show="!collapsed">Student Portal</p>
                    <div class="space-y-1 text-sm font-bold">
                        <a href="{{ route('student.dashboard') }}" class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white hover:bg-red-900/50 hover:text-amber-200 transition" title="My Dashboard">
                            <i class="fa-solid fa-gauge text-amber-300 group-hover:text-white"></i>
                            <span class="text-[13px]" x-show="!collapsed">My Dashboard</span>
                        </a>
                        <a href="{{ route('student.attendance') }}" class="group flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-white hover:bg-red-900/50 hover:text-amber-200 transition" title="Attendance Records">
                            <i class="fa-solid fa-calendar-check text-amber-300 group-hover:text-white"></i>
                            <span class="text-[13px]" x-show="!collapsed">Attendance Records</span>
                        </a>
                    </div>
                </div>
            @endif

        </nav>
    </div>

    <!-- Bottom Logout -->
    <div class="p-3 border-t border-red-900/40 bg-black/15">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-3 py-2.5 px-3 rounded-xl text-xs font-black text-white hover:text-amber-950 hover:bg-amber-400 bg-red-950/50 border border-red-900/50 transition cursor-pointer shadow-xs"
                    title="Sign Out">
                <i class="fa-solid fa-right-from-bracket text-sm text-amber-300 group-hover:text-amber-950"></i>
                <span x-show="!collapsed">Sign Out</span>
            </button>
        </form>
    </div>

</aside>