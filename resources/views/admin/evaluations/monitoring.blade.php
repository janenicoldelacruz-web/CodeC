@extends('layouts.app')

@section('title', 'Faculty Evaluation Monitoring - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- 1. PAGE HEADER -->
    <header class="bg-white border-b-2 border-slate-200 px-6 lg:px-10 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#590d0d] text-amber-300 flex items-center justify-center text-base shadow-xs shrink-0">
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Faculty Evaluation</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Monitor faculty evaluation progress and completion.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- School Year Dropdown -->
            <form method="GET" action="{{ route('admin.evaluations.monitoring') }}" id="filterForm" class="flex items-center gap-3">
                <select name="school_year" onchange="this.form.submit()" class="px-3 py-2 text-xs font-bold bg-slate-100 border-2 border-slate-200 rounded-xl focus:border-[#590d0d] outline-none cursor-pointer">
                    @foreach($schoolYears as $sy)
                        <option value="{{ $sy }}" {{ $activeSchoolYear == $sy ? 'selected' : '' }}>A.Y. {{ $sy }}</option>
                    @endforeach
                </select>

                <!-- Evaluation Period Dropdown -->
                <select name="evaluation_period" onchange="this.form.submit()" class="px-3 py-2 text-xs font-bold bg-slate-100 border-2 border-slate-200 rounded-xl focus:border-[#590d0d] outline-none cursor-pointer">
                    @foreach($evaluationPeriods as $ep)
                        <option value="{{ $ep }}" {{ $activePeriod == $ep ? 'selected' : '' }}>{{ $ep }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Back to Dashboard Button -->
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-[#590d0d] hover:bg-[#430909] text-white text-xs font-black rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </header>

    <main class="p-6 lg:p-10 w-full space-y-8 flex-1">

        <!-- EVALUATION PERIOD STATUS BANNER (Requirement 13) -->
        <div class="p-5 bg-white border-2 border-slate-200 rounded-2xl shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-black">
                    <i class="fa-regular fa-calendar-days"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-900">Evaluation Period: {{ $periodInfo['name'] }}</h4>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Start Date: <span class="font-bold text-slate-700">{{ $periodInfo['start_date'] }}</span> | End Date: <span class="font-bold text-slate-700">{{ $periodInfo['end_date'] }}</span></p>
                </div>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase">
                    Status: {{ $periodInfo['status'] }}
                </span>
            </div>
        </div>

        <!-- 2. OVERALL EVALUATION SUMMARY (CARDS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">
            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between">
                <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Overall Completion Rate</p>
                <h3 class="text-3xl lg:text-4xl font-black text-[#590d0d] tracking-tight mt-1">{{ $overallRate }}%</h3>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-4">
                    <div class="bg-[#590d0d] h-full rounded-full" style="width: {{ $overallRate }}%"></div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between">
                <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Completed Evaluations</p>
                <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($completedCount) }}</h3>
                <span class="text-xs font-bold text-emerald-600 mt-4 flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Successfully submitted</span>
            </div>

            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between">
                <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Not Yet Evaluated</p>
                <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($notYetEvaluatedCount) }}</h3>
                <span class="text-xs font-bold text-amber-600 mt-4 flex items-center gap-1.5"><i class="fa-solid fa-clock"></i> Pending action</span>
            </div>

            <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs flex flex-col justify-between">
                <p class="text-[11px] font-black text-slate-500 uppercase tracking-wider">Total Expected Evaluations</p>
                <h3 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-1">{{ number_format($totalExpected) }}</h3>
                <span class="text-xs font-bold text-slate-400 mt-4">Target total assignments</span>
            </div>
        </div>

        <!-- 3. EVALUATION COMPLETION BY TYPE -->
        <div class="space-y-4">
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Evaluation Completion by Evaluator Type</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($typesData as $key => $type)
                <div class="p-6 bg-white rounded-2xl border-2 border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-600">{{ $type['label'] }}</span>
                        <span class="px-2.5 py-0.5 rounded-md text-xs font-black bg-red-50 text-[#590d0d] border border-red-200">{{ $type['percentage'] }}%</span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <h4 class="text-2xl font-black text-slate-900">{{ $type['completed'] }} / {{ $type['expected'] }}</h4>
                        <span class="text-xs font-semibold text-slate-500">completed</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-[#590d0d] h-full rounded-full" style="width: {{ $type['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 4 & 5. GRAPHS SECTION (Completion Progress & Trend) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Progress Bar Chart -->
            <div class="p-6 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-4">
                <h3 class="text-base font-black text-slate-900">Evaluation Completion Progress</h3>
                <div class="h-64 relative flex items-center justify-center">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>

            <!-- Trend Line Graph -->
            <div class="p-6 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-4">
                <h3 class="text-base font-black text-slate-900">Evaluation Completion Trend</h3>
                <div class="h-64 relative flex items-center justify-center">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 6. FILTERS SECTION -->
        <div class="p-6 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-4">
            <h3 class="text-base font-black text-slate-900">Filter & Search Evaluations</h3>
            <form method="GET" action="{{ route('admin.evaluations.monitoring') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Evaluator Type</label>
                    <select name="evaluator_type" class="w-full p-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl focus:border-[#590d0d] outline-none">
                        <option value="">All Types</option>
                        <option value="student">Students</option>
                        <option value="peer">Peers</option>
                        <option value="personal">Personal / Self-Evaluation</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Department</label>
                    <select name="department" class="w-full p-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl focus:border-[#590d0d] outline-none">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                    <select name="status" class="w-full p-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl focus:border-[#590d0d] outline-none">
                        <option value="">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="not_yet">Not Yet Evaluated</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Search Faculty / Student</label>
                    <input type="text" name="search" placeholder="Name or ID Number..." class="w-full p-2.5 text-xs font-bold border-2 border-slate-200 rounded-xl focus:border-[#590d0d] outline-none">
                </div>
                <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.evaluations.monitoring') }}" class="px-4 py-2 rounded-xl border-2 border-slate-300 text-slate-700 text-xs font-extrabold hover:bg-slate-50">Reset Filters</a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#590d0d] hover:bg-[#430909] text-white text-xs font-black shadow-md">Apply Filters</button>
                </div>
            </form>
        </div>

        <!-- 7. FACULTY EVALUATION SUMMARY TABLE -->
        <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-6">
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Faculty Evaluation Summary</h2>
            <div class="overflow-x-auto rounded-2xl border-2 border-slate-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 uppercase font-black text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-5">Faculty Name</th>
                            <th class="py-4 px-5 text-center">Faculty ID</th>
                            <th class="py-4 px-5">Department</th>
                            <th class="py-4 px-5 text-center">Student Evals</th>
                            <th class="py-4 px-5 text-center">Peer Evals</th>
                            <th class="py-4 px-5 text-center">Self Evals</th>
                            <th class="py-4 px-5 text-center">Overall</th>
                            <th class="py-4 px-5 text-center">Status</th>
                            <th class="py-4 px-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @foreach($facultySummary as $fac)
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="py-4 px-5 font-extrabold text-slate-900">{{ $fac->name }}</td>
                            <td class="py-4 px-5 text-center font-mono text-xs">{{ $fac->id_number }}</td>
                            <td class="py-4 px-5 text-xs text-slate-600">{{ $fac->department }}</td>
                            <td class="py-4 px-5 text-center font-mono text-xs">{{ $fac->student_progress }}</td>
                            <td class="py-4 px-5 text-center font-mono text-xs">{{ $fac->peer_progress }}</td>
                            <td class="py-4 px-5 text-center font-mono text-xs">{{ $fac->personal_progress }}</td>
                            <td class="py-4 px-5 text-center font-black text-[#590d0d]">{{ $fac->overall_rate }}%</td>
                            <td class="py-4 px-5 text-center">
                                @if($fac->status === 'Completed')
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">Completed</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">In Progress</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button onclick="openEvaluationModal('{{ $fac->name }}', '{{ $fac->department }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-[#590d0d] hover:text-white text-slate-700 text-xs font-bold transition">View Details</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 9. NOT YET EVALUATED SECTION -->
        <div class="p-6 lg:p-8 bg-white rounded-3xl border-2 border-slate-200 shadow-xs space-y-6">
            <h2 class="text-lg font-black text-slate-900 tracking-tight">Evaluations Not Yet Completed</h2>
            <div class="overflow-x-auto rounded-2xl border-2 border-slate-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 uppercase font-black text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-5">Evaluator</th>
                            <th class="py-4 px-5">Evaluator Type</th>
                            <th class="py-4 px-5">Faculty Being Evaluated</th>
                            <th class="py-4 px-5 text-center">Period</th>
                            <th class="py-4 px-5 text-center">Status</th>
                            <th class="py-4 px-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @foreach($pendingEvaluations as $pend)
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-5 font-extrabold text-slate-900">{{ $pend->evaluator }}</td>
                            <td class="py-4 px-5 text-xs text-slate-600">
                                @if($pend->evaluator_type === 'Personal / Self-Evaluation')
                                    <span class="font-bold text-[#590d0d]">Personal / Self-Evaluation</span>
                                @else
                                    {{ $pend->evaluator_type }}
                                @endif
                            </td>
                            <td class="py-4 px-5 text-xs text-slate-700 font-bold">{{ $pend->faculty }}</td>
                            <td class="py-4 px-5 text-center text-xs text-slate-500">{{ $pend->period }}</td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">Not Yet Evaluated</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 text-xs font-bold transition">View</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- 10. EVALUATION DETAILS MODAL -->
<div id="evaluationModal" class="fixed inset-0 z-50 bg-slate-950/60 hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border-2 border-slate-200 p-8 space-y-6">
        <div class="flex items-center justify-between border-b pb-4">
            <h3 class="text-lg font-black text-slate-900">Evaluation Assignment Details</h3>
            <button onclick="closeEvaluationModal()" class="text-slate-400 hover:text-slate-700 font-bold text-base"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="space-y-3 text-xs text-slate-700 font-semibold">
            <p><strong>Evaluator Name:</strong> <span id="modalEvaluator" class="text-slate-900 font-bold">Juan Dela Cruz</span></p>
            <p><strong>Evaluator Type:</strong> <span id="modalType" class="text-slate-900 font-bold">Personal / Self-Evaluation</span></p>
            <p><strong>Faculty Being Evaluated:</strong> <span id="modalFaculty" class="text-slate-900 font-bold">Same Faculty Member (Self)</span></p>
            <p><strong>Department:</strong> <span id="modalDept" class="text-slate-900 font-bold">Information Technology</span></p>
            <p><strong>Evaluation Period:</strong> <span class="text-slate-900 font-bold">1st Semester 2026-2027</span></p>
            <p><strong>Date Assigned:</strong> <span class="text-slate-900 font-bold">September 1, 2026</span></p>
            <p><strong>Current Status:</strong> <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">Completed</span></p>
        </div>
        <div class="flex justify-end pt-4 border-t">
            <button onclick="closeEvaluationModal()" class="px-5 py-2 rounded-xl bg-[#590d0d] text-white text-xs font-black">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Progress Bar Chart
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    new Chart(progressCtx, {
        type: 'bar',
        data: {
            labels: ['Students', 'Peers', 'Personal / Self'],
            datasets: [{
                data: [80, 71, 73],
                backgroundColor: ['#590d0d', '#f59e0b', '#3b82f6'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    // Trend Line Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                data: [35, 52, 68, 78],
                borderColor: '#590d0d',
                backgroundColor: 'rgba(89, 13, 13, 0.1)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
});

function openEvaluationModal(name, dept) {
    document.getElementById('evaluationModal').classList.remove('hidden');
    document.getElementById('evaluationModal').classList.add('flex');
}

function closeEvaluationModal() {
    document.getElementById('evaluationModal').classList.add('hidden');
    document.getElementById('evaluationModal').classList.remove('flex');
}
</script>
@endpush
@endsection