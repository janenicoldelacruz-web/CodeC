<!-- Fixed Sidebar Component -->
<aside class="w-64 bg-white border-r border-red-100 flex flex-col justify-between fixed inset-y-0 left-0 z-30 shadow-sm">
    <div>
        <!-- Sidebar Header / Logo -->
        <div class="p-6 border-b border-red-50">
            <div class="flex items-center gap-2">
                <span class="text-xl font-black tracking-wider text-red-600">SIATRACK</span>
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                </svg>
            </div>
            <p class="text-[11px] text-gray-400 font-medium mt-0.5 tracking-tight">Southern Isabela Academy</p>
        </div>

        <!-- Navigation Links with Active States -->
        <nav class="p-4 space-y-1.5">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/dashboard') ? 'bg-red-50 text-red-700 font-semibold border border-red-200' : 'text-gray-600 hover:bg-red-50 hover:text-red-700 font-medium' }} text-sm transition-all duration-200 group">
                <svg class="w-5 h-5 {{ request()->is('admin/dashboard') ? 'text-red-600' : 'text-gray-400 group-hover:text-red-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            <a href="{{ url('/admin/users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/users*') ? 'bg-red-50 text-red-700 font-semibold border border-red-200' : 'text-gray-600 hover:bg-red-50 hover:text-red-700 font-medium' }} text-sm transition-all duration-200 group">
                <svg class="w-5 h-5 {{ request()->is('admin/users*') ? 'text-red-600' : 'text-gray-400 group-hover:text-red-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                User Management
            </a>

            <a href="{{ url('/admin/attendance') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/attendance*') ? 'bg-red-50 text-red-700 font-semibold border border-red-200' : 'text-gray-600 hover:bg-red-50 hover:text-red-700 font-medium' }} text-sm transition-all duration-200 group">
                <svg class="w-5 h-5 {{ request()->is('admin/attendance*') ? 'text-red-600' : 'text-gray-400 group-hover:text-red-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Attendance Records
            </a>

            <a href="{{ url('/admin/evaluations') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/evaluations*') ? 'bg-red-50 text-red-700 font-semibold border border-red-200' : 'text-gray-600 hover:bg-red-50 hover:text-red-700 font-medium' }} text-sm transition-all duration-200 group">
                <svg class="w-5 h-5 {{ request()->is('admin/evaluations*') ? 'text-red-600' : 'text-gray-400 group-hover:text-red-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Faculty Evaluation Mgmt
            </a>

            <a href="{{ url('/admin/reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/reports*') ? 'bg-red-50 text-red-700 font-semibold border border-red-200' : 'text-gray-600 hover:bg-red-50 hover:text-red-700 font-medium' }} text-sm transition-all duration-200 group">
                <svg class="w-5 h-5 {{ request()->is('admin/reports*') ? 'text-red-600' : 'text-gray-400 group-hover:text-red-600' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                Report Generation
            </a>
        </nav>
    </div>

    <!-- Sidebar Footer Logout -->
    <div class="p-4 border-t border-red-50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-2.5 px-4 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 font-semibold rounded-xl text-xs transition-all duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>