@extends('layouts.app')

@section('title', $reportTitle . ' - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70 p-6 lg:p-10 space-y-8">
    
    <!-- Top Navigation & Header -->
    <div class="flex items-center justify-between pb-6 border-b-2 border-slate-200">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-2xl bg-white border-2 border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-100 transition shadow-2xs cursor-pointer">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $reportTitle }}</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Comprehensive deep-dive institutional metrics and summary report</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-black shadow-xs transition inline-flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i> Export / Print Report
            </button>
        </div>
    </div>

    <!-- Dynamic Content Based on Card Type Clicked -->
    <div class="bg-white rounded-3xl border-2 border-slate-200 p-8 shadow-xs space-y-6">
        @if($type === 'attendance')
            <div class="space-y-4">
                <h3 class="text-base font-black text-slate-800">Subject-by-Subject Breakdown Analysis</h3>
                <p class="text-xs text-slate-500 font-bold">Detailed performance metrics across all active class schedules and subject periods.</p>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 font-medium">
                    Subject attendance analytics summary tables and metrics graphs will display here.
                </div>
            </div>
        @elseif($type === 'students')
            <div class="space-y-4">
                <h3 class="text-base font-black text-slate-800">Enrolled Students Master Directory</h3>
                <p class="text-xs text-slate-500 font-bold">Total Enrolled: <span class="text-slate-900 font-black">{{ $totalStudents }}</span> students currently registered.</p>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 font-medium">
                    Comprehensive student demographic breakdowns and strand distribution lists will render here.
                </div>
            </div>
        @elseif($type === 'evaluation')
            <div class="space-y-4">
                <h3 class="text-base font-black text-slate-800">Faculty Evaluation Performance Metrics</h3>
                <p class="text-xs text-slate-500 font-bold">Tracking evaluation progress across <span class="text-slate-900 font-black">{{ $totalFaculty }}</span> registered instructors.</p>
            </div>
        @else
            <div class="space-y-4">
                <h3 class="text-base font-black text-slate-800">Parent SMS Gateway Logs</h3>
                <p class="text-xs text-slate-500 font-bold">Real-time status tracking of notification dispatches.</p>
            </div>
        @endif
    </div>

</div>
@endsection