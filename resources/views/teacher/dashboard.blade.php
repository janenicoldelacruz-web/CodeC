@extends('layouts.app')

@section('title', 'Faculty Dashboard | SIATRACK')

@section('content')
<div class="p-6 md:p-8 max-w-7xl mx-auto w-full">
    
    <!-- Welcome Banner (SIATRACK Theme) -->
    <div class="bg-[#590d0d] rounded-2xl p-6 md:p-8 mb-8 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-center justify-between border border-red-950">
        <!-- Background Accent -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-20 -translate-y-10"></div>
        
        <div class="relative z-10 w-full md:w-auto text-center md:text-left">
            <h1 class="text-2xl md:text-3xl font-black text-amber-300 tracking-tight mb-2">
                Welcome back, {{ $teacher->first_name }}! 👋
            </h1>
            <p class="text-red-200/80 text-sm font-semibold max-w-lg">
                Here is what's happening with your classes today. Manage your schedules, monitor attendance, and review evaluations easily.
            </p>
        </div>
        <div class="relative z-10 mt-6 md:mt-0">
            <div class="px-5 py-2.5 bg-black/20 border border-white/10 rounded-xl flex items-center gap-3 shadow-inner">
                <i class="fa-regular fa-clock text-amber-400 text-lg"></i>
                <div class="text-left">
                    <div class="text-[11px] text-red-200 font-bold uppercase tracking-wider">Current Date</div>
                    <div class="text-sm font-black text-white">{{ now()->format('l, M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
        <!-- Total Classes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chalkboard-user text-2xl"></i>
            </div>
            <div>
                <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">My Active Classes</p>
                <h3 class="text-2xl font-black text-[#590d0d]">{{ $totalClasses }}</h3>
            </div>
        </div>

        <!-- Total Students -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <div>
                <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Total Enrolled Students</p>
                <h3 class="text-2xl font-black text-[#590d0d]">{{ $totalStudents }}</h3>
            </div>
        </div>

        <!-- Today's Classes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-5 hover:shadow-md transition">
            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-calendar-check text-2xl"></i>
            </div>
            <div>
                <p class="text-[11px] font-black text-gray-400 uppercase tracking-wider">Classes Today</p>
                <h3 class="text-2xl font-black text-[#590d0d]">{{ $todaysClasses->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Today's Schedule Table (Real Data) -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-calendar-day text-amber-500"></i>
            My Schedule for Today
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#590d0d] text-amber-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4 w-12">#</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">Section</th>
                            <th class="p-4">Time</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                        @forelse($todaysClasses as $index => $class)
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
                                    <a href="{{ route('teacher.schedule.students', $class->id) }}" class="inline-flex px-3 py-1.5 rounded-lg bg-[#590d0d] text-amber-300 hover:bg-red-950 transition text-xs font-bold items-center gap-1 shadow-sm">
                                        View Class <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <!-- Kung walang klase ngayong araw -->
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-medium text-sm">
                                    <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                        <i class="fa-solid fa-mug-hot text-3xl opacity-50"></i>
                                    </div>
                                    You have no scheduled classes for today. Take a break!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection