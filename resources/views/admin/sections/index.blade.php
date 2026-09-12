@extends('layouts.app')

@section('title', 'Academic Structure - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                <i class="fa-solid fa-layer-group text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-slate-900 tracking-tight">Academic Structure</h1>
                <p class="text-[11px] text-slate-500 font-bold">Manage Class Sections, Grade Levels, and Strands</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 bg-amber-50 text-amber-900 text-[11px] font-black rounded-xl uppercase border border-amber-300 shadow-2xs flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-amber-700"></i>
                <span>{{ $activeSchoolYear }} — {{ $activeSemester }}</span>
            </span>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="pt-6 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-6 flex-1">

        @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            <span>Please complete all required fields correctly.</span>
        </div>
        @endif

        <!-- Single Compact Form Card for Adding Sections -->
        <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 shadow-xs space-y-4 max-w-2xl">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs border border-emerald-200 shrink-0">
                    <i class="fa-solid fa-users-rectangle"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Add New Class Section</h3>
                    <p class="text-[10px] text-slate-500 font-bold">Register a section with its grade level and strand</p>
                </div>
            </div>

            <form action="{{ route('admin.sections.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">Grade Level</label>
                        <input type="text" name="grade_level" placeholder="e.g. Grade 11" required autocomplete="off"
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-lg px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">Section Name</label>
                        <input type="text" name="section_name" placeholder="e.g. Section A" required autocomplete="off"
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-lg px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                    </div>
                    <div>
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">Strand (Optional)</label>
                        <input type="text" name="strand" placeholder="e.g. STEM" autocomplete="off"
                               class="w-full bg-slate-50 border-2 border-slate-200 rounded-lg px-3 py-2 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer">
                        Save Section Record
                    </button>
                </div>
            </form>
        </div>

        <!-- Registered Sections Directory Table -->
        <div class="bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-sm shrink-0">
                    <i class="fa-solid fa-list-check text-amber-300"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">Active Registered Sections Directory</h2>
                    <p class="text-[11px] text-slate-500 font-bold">Complete tracking map of all class groups in the system</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
                            <th class="pb-3 px-4">Grade Level</th>
                            <th class="pb-3 px-4">Section Name</th>
                            <th class="pb-3 px-4">Academic Strand</th>
                            <th class="pb-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                        @forelse($sections as $sec)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 bg-slate-100 rounded-lg text-slate-800 font-black text-[10px] border border-slate-200">
                                    {{ $sec->grade_level }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                {{ $sec->section_name }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg font-black text-[10px]">
                                    {{ $sec->strand ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <!-- Delete Form -->
                                <form action="{{ route('admin.sections.destroy', $sec->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this section?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-[10px] font-black transition cursor-pointer inline-flex items-center gap-1 border border-red-200">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400 font-bold space-y-1">
                                <div class="text-xl text-slate-300"><i class="fa-solid fa-folder-open"></i></div>
                                <p class="text-xs">No class sections registered yet. Use the form above to add records.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
@endsection