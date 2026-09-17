<!-- Section 1: KPI Summary Metrics -->
<div class="space-y-3 w-full">
    <div class="flex items-center justify-between">
        <p class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-[#590d0d] text-xs"></i>
            <span>Institutional Metrics Overview</span>
        </p>
    </div>

    <!-- 3-column grid layout -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full">

        <!-- 1. Total Students Enrolled -->
        <a href="{{ route('admin.students.analytics') }}" class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-amber-300 transition w-full block text-left group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Total Students</p>
                    <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalStudents ?? 0) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 border-2 border-amber-200 text-amber-700 flex items-center justify-center text-xl shadow-xs shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
            <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="studentSparkline" class="pointer-events-none"></canvas></div>
            <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                <span>View Analytics</span>
                <i class="fa-solid fa-arrow-right text-amber-500 group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- 2. Daily Attendance Rate -->
        <a href="{{ route('admin.attendance.rate') }}" class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-red-300 transition w-full block text-left group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Attendance Rate</p>
                    <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ $attendanceRate ?? '0%' }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-red-50 border-2 border-red-200 text-[#590d0d] flex items-center justify-center text-xl shadow-xs shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-2">
                    <div class="bg-[#590d0d] h-full rounded-full transition-all duration-500" style="width: {{ min(100, (float)($attendanceRate ?? 0)) }}%"></div>
                </div>
                <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="attendanceSparkline" class="pointer-events-none"></canvas></div>
                <div class="flex items-center justify-between text-xs text-slate-600 font-bold pt-1">
                    <span class="text-[#590d0d] font-mono font-black">Present: {{ $presentTodayCount ?? 0 }} / {{ $totalStudents ?? 0 }}</span>
                    <i class="fa-solid fa-arrow-right text-[#590d0d] group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>

        <!-- 3. Faculty Evaluation Progress -->
        <a href="{{ route('admin.evaluations.monitoring') }}" class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between min-h-[155px] hover:shadow-md hover:border-blue-300 transition w-full block text-left group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Faculty Evaluation</p>
                    <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ $evalProgress ?? '0%' }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 border-2 border-blue-200 text-blue-700 flex items-center justify-center text-xl shadow-xs shrink-0 group-hover:scale-105 transition">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
            </div>
            <div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-2">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ min(100, (float)($evalProgress ?? 0)) }}%"></div>
                </div>
                <div style="height: 26px; width: 100%; position: relative; margin: 4px 0;"><canvas id="evalSparkline" class="pointer-events-none"></canvas></div>
                <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600 font-bold">
                    <span>View Monitoring</span>
                    <i class="fa-solid fa-arrow-right text-blue-600 group-hover:translate-x-1 transition"></i>
                </div>
            </div>
        </a>

    </div>
</div>