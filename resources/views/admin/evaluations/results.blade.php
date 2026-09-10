@extends('layouts.app')

@section('title', 'Faculty Evaluation Results - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70 font-sans pb-12">

    <!-- Header Navigation -->
    <header class="bg-white border-b-2 border-slate-200 px-6 lg:px-10 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.evaluations') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center justify-center text-slate-600 transition shadow-2xs">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">Faculty Evaluation Results & Analytics</h1>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase">ANALYTICS LIVE</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Consolidated 360-degree performance ratings and appraisal reports across all instructors</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.attendance.export') ?? '#' }}" class="px-4 py-2 bg-[#8b1818] hover:bg-[#701313] text-white text-xs font-black rounded-xl shadow-xs flex items-center gap-2 transition">
                <i class="fa-solid fa-file-pdf text-amber-300"></i>
                <span>Export Appraisal Summary</span>
            </a>
        </div>
    </header>

    <main class="p-6 lg:p-10 max-w-[1600px] w-full mx-auto space-y-8 flex-1">

        <!-- ================= SECTION 1: SUMMARY KPI METRICS ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white border-2 border-slate-200 rounded-2xl shadow-xs flex flex-col justify-between">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Overall Average Score</p>
                <h4 class="text-3xl font-black text-slate-900 mt-1 font-mono">
                    {{ $averageScore > 0 ? number_format((float)$averageScore, 1) : '0.0' }} <span class="text-xs text-slate-400 font-bold">/ 5.0</span>
                </h4>
                <div class="mt-3 pt-2 border-t border-slate-100 flex justify-between text-[11px] font-bold">
                    <span class="text-slate-500">Benchmark</span>
                    <span class="text-emerald-600 font-black">Passed (Standard)</span>
                </div>
            </div>

            <div class="p-5 bg-white border-2 border-slate-200 rounded-2xl shadow-xs flex flex-col justify-between">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Submissions</p>
                <h4 class="text-3xl font-black text-slate-900 mt-1 font-mono">{{ number_format($totalSubmissions ?? 0) }}</h4>
                <div class="mt-3 pt-2 border-t border-slate-100 flex justify-between text-[11px] font-bold">
                    <span class="text-slate-500">Review Entries</span>
                    <span class="text-blue-600 font-black">Synced</span>
                </div>
            </div>

            <div class="p-5 bg-white border-2 border-slate-200 rounded-2xl shadow-xs flex flex-col justify-between">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Faculty Monitored</p>
                <h4 class="text-3xl font-black text-slate-900 mt-1 font-mono">{{ number_format($facultyList->total() ?? 0) }}</h4>
                <div class="mt-3 pt-2 border-t border-slate-100 flex justify-between text-[11px] font-bold">
                    <span class="text-slate-500">Instructors</span>
                    <span class="text-amber-600 font-black">Active Evaluated</span>
                </div>
            </div>

            <div class="p-5 bg-white border-2 border-slate-200 rounded-2xl shadow-xs flex flex-col justify-between">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Evaluation Cycle</p>
                <h4 class="text-lg font-black text-slate-900 mt-2">DepEd Term 1</h4>
                <div class="mt-3 pt-2 border-t border-slate-100 flex justify-between text-[11px] font-bold">
                    <span class="text-slate-500">Framework</span>
                    <span class="text-emerald-700 font-black">3-Term Framework</span>
                </div>
            </div>
        </div>

        <!-- ================= SECTION 2: FACULTY APPRAISAL TABLE ================= -->
        <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 lg:p-8 space-y-6 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b-2 border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-black shadow-2xs">
                        <i class="fa-solid fa-ranking-star text-[#8b1818]"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Instructor Appraisal Leaderboard</h2>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">Rankings based on consolidated student, peer, and supervisor feedback</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.evaluations.results') }}" class="relative w-full md:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter faculty name or ID..."
                           class="w-full pl-9 pr-4 py-2.5 text-xs font-bold border-2 border-slate-200 rounded-2xl focus:border-[#8b1818] outline-none bg-white transition shadow-2xs">
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-2xl border-2 border-slate-200 shadow-2xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 uppercase font-black tracking-wider text-xs border-b-2 border-slate-200">
                            <th class="py-4 px-5">Faculty Member</th>
                            <th class="py-4 px-5 text-center">School ID</th>
                            <th class="py-4 px-5 text-center">Department / Strand</th>
                            <th class="py-4 px-5 text-center">Overall Score</th>
                            <th class="py-4 px-5 text-center">Descriptive Rating</th>
                            <th class="py-4 px-5 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                        @forelse($facultyList as $fac)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-red-100 text-[#8b1818] font-black text-xs flex items-center justify-center shadow-2xs">
                                        {{ strtoupper(substr($fac->first_name, 0, 1)) }}{{ strtoupper(substr($fac->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block leading-tight">{{ $fac->first_name }} {{ $fac->last_name }}</span>
                                        <span class="text-[11px] font-normal text-slate-400">{{ $fac->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-center font-mono font-bold text-xs text-slate-600">
                                {{ $fac->id_number ?? 'FAC-N/A' }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold">
                                    {{ $fac->strand ?? 'Senior High Faculty' }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="text-sm font-black font-mono text-slate-900">4.8</span>
                                <span class="text-xs text-slate-400 font-bold">/ 5.0</span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    Outstanding
                                </span>
                            </td>
                            <td class="py-4 px-5 text-center">
                                <button type="button" onclick="alert('Viewing comprehensive evaluation breakdown for {{ $fac->first_name }} {{ $fac->last_name }}.')" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-extrabold border border-slate-300 transition cursor-pointer">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-bold">No faculty records found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $facultyList->links() }}
            </div>
        </div>
    </main>
</div>
@endsection