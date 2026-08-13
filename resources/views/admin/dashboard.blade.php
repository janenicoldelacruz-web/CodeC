<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIATRACK - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased flex m-0 p-0 overflow-x-hidden text-gray-800">

    <!-- Reusable Sidebar Component -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <div class="ml-64 flex-1 flex flex-col min-h-screen">
        
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-2xs">
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Admin Dashboard</h1>
            <div class="flex items-center gap-4">
                <button class="relative text-gray-500 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-50">
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-600 rounded-full animate-pulse"></span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <div class="h-6 w-px bg-gray-200"></div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-100 text-red-700 font-bold flex items-center justify-center text-sm shadow-2xs">A</div>
                    <span class="text-sm font-semibold text-gray-700">Admin Profile</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-8 max-w-7xl mx-auto w-full">
            
            <div class="mb-6">
                <h2 class="text-base font-bold text-gray-900">KPI Summary Cards</h2>
            </div>

            <!-- KPI Summary Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-2xs hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-500 group-hover:scale-105 transition-transform flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ number_format($totalStudents ?? 0) }}</h3>
                        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">Total Students Enrolled</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-2xs hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 group-hover:scale-105 transition-transform flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $attendanceRate ?? '0.0' }}%</h3>
                        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">Daily Attendance Rate</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-2xs hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 group-hover:scale-105 transition-transform flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $evaluationProgress ?? '0' }}% <span class="text-xs font-semibold text-gray-400">Completed</span></h3>
                        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">Faculty Evaluation Progress</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-2xs hover:shadow-md hover:border-red-300 transition-all duration-300 flex flex-col justify-between group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 group-hover:scale-105 transition-transform flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $activeSmsCount ?? 0 }}</h3>
                        <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">Active SMS Today</p>
                    </div>
                </div>

            </div>

            <!-- Real-Time Activity Feed Table Card -->
            <div class="bg-white rounded-2xl border border-red-200 shadow-2xs p-6">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-gray-900">Real-Time Activity Feed: Live Attendance Stream</h2>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-red-50/60 text-red-900 text-xs font-bold uppercase tracking-wider border-b border-red-100">
                                <th class="py-3.5 px-4">Student Name</th>
                                <th class="py-3.5 px-4">Grade/Section</th>
                                <th class="py-3.5 px-4">Time In</th>
                                <th class="py-3.5 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700 font-medium">
                            @forelse($liveAttendances ?? [] as $index => $attendance)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-red-50/30 transition">
                                    <td class="py-3.5 px-4 font-bold text-gray-900">{{ $attendance->student->name ?? 'Unknown Student' }}</td>
                                    <td class="py-3.5 px-4 text-gray-500">{{ $attendance->student->grade_section ?? 'N/A' }}</td>
                                    <td class="py-3.5 px-4 text-gray-600">{{ \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-200/60 text-[10px]">
                                            {{ $attendance->status ?? 'On-Time' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-400 text-sm font-medium">
                                        No live attendance logs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>