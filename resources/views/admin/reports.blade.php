@extends('layouts.app')

@section('title', 'Report Generation - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar (Spaced Left and Vertical) -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-base shadow-xs shrink-0">
                <i class="fa-solid fa-chart-column text-amber-300"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Report Generation Center</h1>
                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase tracking-wide shadow-2xs">
                        A.Y. {{ date('Y') }}-{{ date('Y') + 1 }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Export institutional summaries, gate attendance logs, and faculty evaluation analytics</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-100 border-2 border-slate-200 shadow-2xs">
                <i class="fa-solid fa-file-export text-slate-500 text-xs"></i>
                <span class="text-xs font-black text-slate-700 tracking-wide uppercase">CSV Data Export Ready</span>
            </div>
        </div>
    </header>

    <!-- Main Content Container (pt-12 Clearance Below Header, pl-8 lg:pl-12 from Sidebar) -->
    <main class="pt-12 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-8 flex-1">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-xs font-bold rounded-2xl flex items-center gap-2.5 shadow-xs w-full">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- ================= Export Cards Grid ================= -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
            
            <!-- 1. Attendance Report Card -->
            <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs flex flex-col justify-between space-y-6 hover:shadow-md hover:border-red-300 transition w-full">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 border-2 border-red-200 text-[#8b1818] flex items-center justify-center text-xl shadow-xs shrink-0">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Attendance Logs Report</h3>
                        <p class="text-xs text-slate-500 font-bold mt-1 leading-relaxed">Full gate tap history, time-in timestamps, status flags, and student punctuality records.</p>
                    </div>
                    
                    <div class="pt-4 border-t-2 border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Available Entries</span>
                            <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalLogs ?? 0) }}</span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#8b1818]"></span>
                    </div>
                </div>

                <div>
                    @if(\Illuminate\Support\Facades\Route::has('admin.attendance.export'))
                        <a href="{{ route('admin.attendance.export') }}" 
                           class="w-full py-3.5 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-red-950/20 hover:shadow-xl transition-all duration-150 active:scale-[0.98] border-b-4 border-[#5e0f0f]">
                            <i class="fa-solid fa-file-csv text-sm text-amber-300"></i> 
                            <span>Download Attendance CSV</span>
                        </a>
                    @else
                        <button disabled class="w-full py-3.5 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed">
                            <i class="fa-solid fa-ban text-xs"></i> Export Unavailable
                        </button>
                    @endif
                </div>
            </div>

            <!-- 2. User Masterlist Report Card -->
            <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs flex flex-col justify-between space-y-6 hover:shadow-md hover:border-blue-300 transition w-full">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 border-2 border-blue-200 text-blue-700 flex items-center justify-center text-xl shadow-xs shrink-0">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">User Masterlist Directory</h3>
                        <p class="text-xs text-slate-500 font-bold mt-1 leading-relaxed">Complete registered students, faculty members, and institutional accounts directory.</p>
                    </div>
                    
                    <div class="pt-4 border-t-2 border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Students & Faculty</span>
                            <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format(($totalStudents ?? 0) + ($totalFaculty ?? 0)) }}</span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    </div>
                </div>

                <div>
                    @if(\Illuminate\Support\Facades\Route::has('admin.users.export'))
                        <a href="{{ route('admin.users.export') }}" 
                           class="w-full py-3.5 rounded-2xl bg-slate-900 hover:bg-black text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-slate-900/20 hover:shadow-xl transition-all duration-150 active:scale-[0.98] border-b-4 border-slate-950">
                            <i class="fa-solid fa-file-csv text-sm text-emerald-400"></i> 
                            <span>Download User Directory</span>
                        </a>
                    @else
                        <button disabled class="w-full py-3.5 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed">
                            <i class="fa-solid fa-ban text-xs"></i> Export Unavailable
                        </button>
                    @endif
                </div>
            </div>

            <!-- 3. Faculty Evaluations Report Card -->
            <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs flex flex-col justify-between space-y-6 hover:shadow-md hover:border-amber-300 transition w-full">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border-2 border-amber-200 text-amber-700 flex items-center justify-center text-xl shadow-xs shrink-0">
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Faculty Evaluations Report</h3>
                        <p class="text-xs text-slate-500 font-bold mt-1 leading-relaxed">Student rating submissions, instructional scores, and constructive qualitative remarks.</p>
                    </div>
                    
                    <div class="pt-4 border-t-2 border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Submitted Forms</span>
                            <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalEvals ?? 0) }}</span>
                        </div>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    </div>
                </div>

                <div>
                    @if(\Illuminate\Support\Facades\Route::has('admin.evaluations.export'))
                        <a href="{{ route('admin.evaluations.export') }}" 
                           class="w-full py-3.5 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-amber-950/20 hover:shadow-xl transition-all duration-150 active:scale-[0.98] border-b-4 border-amber-800">
                            <i class="fa-solid fa-file-csv text-sm text-amber-200"></i> 
                            <span>Download Evaluations CSV</span>
                        </a>
                    @else
                        <button disabled class="w-full py-3.5 rounded-2xl bg-slate-200 text-slate-400 font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed">
                            <i class="fa-solid fa-ban text-xs"></i> Export Unavailable
                        </button>
                    @endif
                </div>
            </div>

        </div>

        <!-- ================= Custom Date Range Filter Box ================= -->
        <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-6 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b-2 border-slate-100 pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-red-100 text-[#8b1818] flex items-center justify-center font-black text-lg shadow-2xs shrink-0">
                        <i class="fa-solid fa-calendar-week"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Custom Date Range Filter Export</h2>
                        <p class="text-xs text-slate-500 font-bold mt-0.5">Filter attendance logs by exact timeframe before downloading</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ \Illuminate\Support\Facades\Route::has('admin.attendance.export') ? route('admin.attendance.export') : '#' }}" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" 
                           class="w-full py-2.5 px-3.5 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-[#8b1818] transition shadow-2xs text-slate-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" 
                           class="w-full py-2.5 px-3.5 text-xs font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:border-[#8b1818] transition shadow-2xs text-slate-700">
                </div>

                <div>
                    <button type="submit" 
                            class="w-full py-3 rounded-xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-md shadow-red-950/20 hover:shadow-lg transition-all duration-150 active:scale-[0.98] border-b-4 border-[#5e0f0f] flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter text-xs text-amber-300"></i>
                        <span>Export Filtered Attendance CSV</span>
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection