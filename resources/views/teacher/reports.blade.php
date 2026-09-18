@extends('layouts.app')

@section('title', 'Reports & Analytics | SIATRACK')

@section('content')
<div class="p-6 md:p-8 max-w-7xl mx-auto w-full">
    
    <!-- Page Header (SIATRACK Theme) -->
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white p-1 border-2 border-amber-300 shadow-sm flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-pie text-[#590d0d] text-lg"></i>
            </div>
            Reports & Analytics
        </h1>
        <p class="text-sm font-semibold text-gray-500 mt-2 ml-1">
            Generate and view attendance summaries, class lists, and evaluation records.
        </p>
    </div>

    <!-- Report Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
        <!-- Attendance Report Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col items-center text-center hover:shadow-md transition group">
            <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                <i class="fa-solid fa-clipboard-user text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-1">Attendance Report</h3>
            <p class="text-xs text-gray-500 mb-4 px-2">Download NFC tap logs and student attendance summaries.</p>
            <a href="{{ route('teacher.attendance.export') }}" class="mt-auto bg-[#590d0d] text-amber-300 text-xs font-bold px-4 py-2 rounded-lg w-full hover:bg-red-950 transition">
                Generate CSV
            </a>
        </div>

        <!-- Class List Report Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col items-center text-center hover:shadow-md transition group">
            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                <i class="fa-solid fa-users-rectangle text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-1">Class Masterlist</h3>
            <p class="text-xs text-gray-500 mb-4 px-2">View the complete list of students currently enrolled in your sections.</p>
            <a href="{{ route('teacher.students') }}" class="mt-auto bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold px-4 py-2 rounded-lg w-full hover:bg-emerald-200 transition">
                View Directory
            </a>
        </div>

        <!-- Evaluation Report Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col items-center text-center hover:shadow-md transition group">
            <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                <i class="fa-solid fa-star-half-stroke text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 mb-1">Evaluation Results</h3>
            <p class="text-xs text-gray-500 mb-4 px-2">Review your peer and self-evaluation summaries and ratings.</p>
            <a href="{{ route('teacher.evaluation.report') }}" class="mt-auto bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold px-4 py-2 rounded-lg w-full hover:bg-purple-200 transition">
                View Ratings
            </a>
        </div>
    </div>

    <!-- Active Classes Summary Table (Real Data) -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-book-open text-amber-500"></i>
            My Reporting Sections (Active Classes)
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#590d0d] text-amber-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4 w-12">#</th>
                            <th class="p-4">Subject / Course</th>
                            <th class="p-4">Section Name</th>
                            <th class="p-4">Schedule</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                        @forelse($myClasses as $index => $class)
                            <tr class="hover:bg-amber-50/50 transition duration-150">
                                <td class="p-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="p-4 font-black text-[#590d0d]">
                                    {{ $class->subject_name ?? ($class->subject ?? 'Subject Not Set') }}
                                </td>
                                <td class="p-4">
                                    <span class="bg-[#590d0d]/10 text-[#590d0d] px-2.5 py-1 rounded-md text-xs font-bold border border-[#590d0d]/20">
                                        {{ $class->section ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4 text-[12px] font-semibold text-gray-500">
                                    <i class="fa-regular fa-clock mr-1 text-amber-500"></i> 
                                    {{ $class->time_start ?? '--:--' }} - {{ $class->time_end ?? '--:--' }}
                                </td>
                                <td class="p-4 text-center">
                                    <!-- Dinudugtong ang classList view para mas detalyadong report -->
                                    <a href="{{ route('teacher.schedule.students', $class->id) }}" class="inline-flex w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 hover:bg-teal-500 hover:text-white transition items-center justify-center" title="Generate Class Report">
                                        <i class="fa-solid fa-file-export"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <!-- Kung walang assigned classes sa teacher sa database -->
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-medium text-sm">
                                    <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                        <i class="fa-solid fa-box-open text-3xl opacity-50"></i>
                                    </div>
                                    No active class sections assigned to you for reporting.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3.5 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-500"></i> 
                Select a class section to view detailed analytics and download specific reports.
            </div>
        </div>
    </div>

</div>
@endsection