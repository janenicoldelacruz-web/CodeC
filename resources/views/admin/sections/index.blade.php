@extends('layouts.app')

@section('title', 'Academic Structure - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70"
     x-data="{ 
         openGrades: {},
         activeFilter: 'all',
         editModal: false,
         editId: '',
         editGrade: '',
         editName: '',
         editStrand: '',

         toggleGrade(grade) {
             this.openGrades[grade] = !this.openGrades[grade];
         },

         openEdit(id, grade, name, strand) {
             this.editId = id;
             this.editGrade = grade;
             this.editName = name;
             this.editStrand = strand !== 'null' ? strand : '';
             this.editModal = true;
         },

         isJuniorHigh(grade) {
             return /7|8|9|10/i.test(grade);
         },

         isSeniorHigh(grade) {
             return /11|12/i.test(grade);
         }
     }">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 px-8 lg:px-12 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                <i class="fa-solid fa-layer-group text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-slate-900 tracking-tight">Academic Structure</h1>
                <p class="text-[11px] text-slate-500 font-bold">Manage Class Sections, Grade Levels, and Strands</p>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="p-6 lg:p-10 w-full max-w-6xl mx-auto space-y-6 flex-1">

        @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            <span>Please complete all required fields correctly.</span>
        </div>
        @endif

        <!-- Compact Form Card for Adding Sections -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs space-y-4">
            <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs border border-emerald-200 shrink-0">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Add New Class Section</h3>
                </div>
            </div>

            <form action="{{ route('admin.sections.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Grade Level</label>
                        <input type="text" name="grade_level" placeholder="e.g. GRADE 11" required autocomplete="off"
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] uppercase transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Section Name</label>
                        <input type="text" name="section_name" placeholder="e.g. AMBER" required autocomplete="off"
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] uppercase transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Strand (Optional)</label>
                        <input type="text" name="strand" placeholder="e.g. HUMMS" autocomplete="off"
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] uppercase transition">
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-[11px] font-black uppercase tracking-wider transition shadow-xs cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Save Section
                    </button>
                </div>
            </form>
        </div>

        <!-- Directory Container -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-2xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-xs shadow-xs shrink-0">
                        <i class="fa-solid fa-list-check text-amber-300"></i>
                    </div>
                    <div>
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-900">Registered Sections Directory</h2>
                        <p class="text-[10px] text-slate-400 font-bold">Organized from lowest to highest grade level</p>
                    </div>
                </div>

                <!-- Junior / Senior Filter Tabs -->
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                    <button type="button" @click="activeFilter = 'all'" 
                            :class="activeFilter === 'all' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-black transition cursor-pointer">
                        All
                    </button>
                    <button type="button" @click="activeFilter = 'jhs'" 
                            :class="activeFilter === 'jhs' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-black transition cursor-pointer">
                        Junior High (7–10)
                    </button>
                    <button type="button" @click="activeFilter = 'shs'" 
                            :class="activeFilter === 'shs' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'"
                            class="px-3.5 py-1.5 rounded-lg text-[11px] font-black transition cursor-pointer">
                        Senior High (11–12)
                    </button>
                </div>
            </div>

            @php 
                // Pag-ayos ng mga grade level mula lowest hanggang highest pataas gamit ang numeric value
                $sortedSections = $sections->sortBy(function($item) {
                    preg_match('/\d+/', $item->grade_level, $matches);
                    return isset($matches[0]) ? (int)$matches[0] : 0;
                });
                $groupedSections = $sortedSections->groupBy('grade_level'); 
            @endphp

            <div class="space-y-3">
                @forelse($groupedSections as $gradeLevel => $gradeSections)
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition bg-white shadow-2xs"
                     x-show="activeFilter === 'all' || (activeFilter === 'jhs' && isJuniorHigh('{{ $gradeLevel }}')) || (activeFilter === 'shs' && isSeniorHigh('{{ $gradeLevel }}'))">
                    
                    <!-- Accordion Header -->
                    <button type="button" @click="toggleGrade('{{ $gradeLevel }}')" 
                            class="w-full px-5 py-4 flex items-center justify-between bg-white hover:bg-slate-50/80 transition cursor-pointer text-left">
                        <div class="flex items-center gap-3">
                            <span class="px-3.5 py-1.5 bg-[#8b1818] text-white font-black text-xs rounded-xl shadow-2xs uppercase">
                                {{ $gradeLevel }}
                            </span>
                            <span class="text-xs font-bold text-slate-500">
                                {{ $gradeSections->count() }} {{ Str::plural('Section', $gradeSections->count()) }} Registered
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400 font-bold text-xs">
                            <span class="text-[10px] font-black uppercase tracking-wider" x-text="openGrades['{{ $gradeLevel }}'] ? 'Hide Sections' : 'View Sections'"></span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-200" :class="openGrades['{{ $gradeLevel }}'] ? 'rotate-180 text-[#8b1818]' : ''"></i>
                        </div>
                    </button>

                    <!-- Accordion Content Table -->
                    <div x-show="openGrades['{{ $gradeLevel }}']" x-transition.origin.top style="display: none;" class="border-t border-slate-100 bg-white p-2">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                        <th class="py-2.5 px-4">Section Name</th>
                                        <th class="py-2.5 px-4">Academic Strand</th>
                                        <th class="py-2.5 px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs font-bold text-slate-700">
                                    @foreach($gradeSections as $sec)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-3 px-4 font-black text-slate-900 flex items-center gap-2.5 uppercase">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            {{ $sec->section_name }}
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($sec->strand)
                                            <span class="px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg font-black text-[10px] uppercase">
                                                {{ $sec->strand }}
                                            </span>
                                            @else
                                            <span class="text-slate-300 text-[10px] uppercase">N/A</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <div class="inline-flex items-center gap-1.5">
                                                <!-- Edit Icon Only Button -->
                                                <button type="button" @click="openEdit('{{ $sec->id }}', '{{ $sec->grade_level }}', '{{ $sec->section_name }}', '{{ $sec->strand }}')"
                                                        title="Edit Section"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs transition cursor-pointer">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                <!-- Delete Icon Only Form -->
                                                <form action="{{ route('admin.sections.destroy', $sec->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this section?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            title="Delete Section"
                                                            class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 flex items-center justify-center text-xs transition cursor-pointer">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                @empty
                <div class="py-12 text-center text-slate-400 font-bold space-y-1 bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="text-xl text-slate-300"><i class="fa-solid fa-folder-open"></i></div>
                    <p class="text-xs">No class sections registered yet. Use the form above to add records.</p>
                </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- ================= EDIT SECTION MODAL ================= -->
    <div x-show="editModal" x-transition class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/50 backdrop-blur-xs p-4" style="display: none;" x-cloak>
        <div @click.outside="editModal = false" class="bg-white rounded-3xl border border-slate-100 p-8 max-w-md w-full shadow-2xl space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Edit Class Section</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form :action="'/admin/sections/' + editId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Grade Level</label>
                    <input type="text" name="grade_level" x-model="editGrade" required autocomplete="off"
                           @input="editGrade = editGrade.toUpperCase()"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] uppercase transition">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Section Name</label>
                    <input type="text" name="section_name" x-model="editName" required autocomplete="off"
                           @input="editName = editName.toUpperCase()"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] uppercase transition">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Strand (Optional)</label>
                    <input type="text" name="strand" x-model="editStrand" autocomplete="off"
                           @input="editStrand = editStrand.toUpperCase()"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] uppercase transition">
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button @click="editModal = false" type="button" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider cursor-pointer transition">
                        Cancel
                    </button>
                    <button type="submit" class="w-full py-3 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider shadow-xs cursor-pointer transition">
                        Update Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection