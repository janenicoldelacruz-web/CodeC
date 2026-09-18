@extends('layouts.app')

@section('title', 'Student Directory | SIATRACK')

@section('content')
<div class="p-6 md:p-8 max-w-7xl mx-auto w-full">
    
    <!-- Page Header (SIATRACK Theme) -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white p-1 border-2 border-amber-300 shadow-sm flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-user-graduate text-[#590d0d] text-lg"></i>
                </div>
                Student Directory
            </h1>
            <p class="text-sm font-semibold text-gray-500 mt-2 ml-1">
                View real-time master list of enrolled students.
            </p>
        </div>
        
        <!-- Search Bar -->
        <div class="relative w-full md:w-72">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
            </div>
            <input type="text" placeholder="Search student name or ID..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 shadow-sm outline-none transition text-sm font-medium text-gray-700 bg-white">
        </div>
    </div>

    <!-- Student List Table -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-users text-amber-500"></i>
            Master List
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#590d0d] text-amber-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4">#</th>
                            <th class="p-4">Student ID / LRN</th>
                            <th class="p-4">Full Name</th>
                            <th class="p-4">Gender</th>
                            <th class="p-4">Contact / Email</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-amber-50/50 transition duration-150">
                                <td class="p-4">{{ $index + 1 }}</td>
                                <td class="p-4">
                                    <span class="bg-[#590d0d]/10 text-[#590d0d] px-2.5 py-1 rounded-md text-xs font-bold border border-[#590d0d]/20">
                                        {{ $student->id_number ?? 'No ID' }}
                                    </span>
                                </td>
                                <td class="p-4 font-black text-gray-800">
                                    {{ $student->last_name }}, {{ $student->first_name }}
                                </td>
                                <td class="p-4 text-[13px]">
                                    @if(($student->gender ?? '') == 1 || strtolower($student->gender ?? '') == 'male')
                                        <span class="text-blue-600"><i class="fa-solid fa-mars mr-1"></i> Male</span>
                                    @elseif(($student->gender ?? '') == 2 || strtolower($student->gender ?? '') == 'female')
                                        <span class="text-pink-600"><i class="fa-solid fa-venus mr-1"></i> Female</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="p-4 text-[12px]">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-700">{{ $student->email }}</span>
                                        @if($student->phone_number)
                                            <span class="text-gray-500">{{ $student->phone_number }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <button class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 hover:bg-teal-500 hover:text-white transition" title="View Profile">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <!-- Kung walang estudyante sa database -->
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-400 font-medium text-sm">
                                    <i class="fa-regular fa-folder-open text-3xl mb-3 block opacity-50"></i>
                                    No student records found in the database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3.5 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-500"></i> 
                List displays all registered users with the Student Role.
            </div>
        </div>
    </div>

</div>
@endsection