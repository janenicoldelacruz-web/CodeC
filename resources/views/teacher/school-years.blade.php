@extends('layouts.app')

@section('title', 'School Year & Sections | SIATRACK')

@section('content')
<div class="p-6 md:p-8 max-w-7xl mx-auto w-full">
    
    <!-- Page Header (SIATRACK Theme) -->
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white p-1 border-2 border-amber-300 shadow-sm flex items-center justify-center shrink-0">
                <i class="fa-solid fa-layer-group text-[#590d0d] text-lg"></i>
            </div>
            School Year and Sections
        </h1>
        <p class="text-sm font-semibold text-gray-500 mt-2 ml-1">
            View real-time school year and section information. Contact administrator to add new records.
        </p>
    </div>

    <!-- School Year Overview Table -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-calendar-days text-amber-500"></i>
            School Year Overview
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#590d0d] text-amber-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4">#</th>
                            <th class="p-4">School Year</th>
                            <th class="p-4">Start Date</th>
                            <th class="p-4">End Date</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                        @forelse($schoolYears as $index => $sy)
                            <tr class="hover:bg-amber-50/50 transition duration-150">
                                <td class="p-4">{{ $index + 1 }}</td>
                                <td class="p-4 font-black text-[#590d0d]">{{ $sy->name }}</td>
                                <td class="p-4 text-[13px]">
                                    <i class="fa-regular fa-calendar-check text-green-600 mr-1.5"></i> 
                                    {{ $sy->start_date ? \Carbon\Carbon::parse($sy->start_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="p-4 text-[13px]">
                                    <i class="fa-regular fa-calendar-xmark text-red-600 mr-1.5"></i> 
                                    {{ $sy->end_date ? \Carbon\Carbon::parse($sy->end_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ ($sy->status ?? '') === 'Completed' ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700' }}">
                                        {{ $sy->status ?? 'Active' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <!-- Kung walang data sa database ng SIATRACK -->
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 font-medium text-sm">
                                    <i class="fa-regular fa-folder-open text-3xl mb-3 block opacity-50"></i>
                                    No school year records found in the database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Footer hint -->
            <div class="p-3.5 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-500"></i> 
                Record additions and modifications are handled by the Administrator portal.
            </div>
        </div>
    </div>

    <!-- Section Overview Table -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chalkboard-user text-amber-500"></i>
            Section Overview
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#590d0d] text-amber-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4">#</th>
                            <th class="p-4">Section Name</th>
                            <th class="p-4">Grade Level</th>
                            <th class="p-4">AM Time In</th>
                            <th class="p-4">AM Time Out</th>
                            <th class="p-4">PM Time In</th>
                            <th class="p-4">PM Time Out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                        @forelse($sections as $index => $section)
                            <tr class="hover:bg-amber-50/50 transition duration-150">
                                <td class="p-4">{{ $index + 1 }}</td>
                                <td class="p-4 font-black text-[#590d0d]">{{ $section->name }}</td>
                                <td class="p-4">
                                    <span class="bg-[#590d0d]/10 text-[#590d0d] px-2.5 py-1 rounded-md text-xs font-bold border border-[#590d0d]/20">
                                        {{ $section->grade_level ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-4 text-[12px] font-semibold">{{ $section->am_in ?? '--:--' }}</td>
                                <td class="p-4 text-[12px] font-semibold">{{ $section->am_out ?? '--:--' }}</td>
                                <td class="p-4 text-[12px] font-semibold">{{ $section->pm_in ?? '--:--' }}</td>
                                <td class="p-4 text-[12px] font-semibold">{{ $section->pm_out ?? '--:--' }}</td>
                            </tr>
                        @empty
                            <!-- Kung walang data sa database ng SIATRACK -->
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-400 font-medium text-sm">
                                    <i class="fa-regular fa-folder-open text-3xl mb-3 block opacity-50"></i>
                                    No section records found in the database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3.5 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center gap-2">
                <i class="fa-solid fa-users-rectangle text-amber-500"></i> 
                Sections are strictly synchronized with real-time class enrollments.
            </div>
        </div>
    </div>

</div>
@endsection