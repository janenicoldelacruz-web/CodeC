@extends('layouts.app')

@section('title', 'Add New User - SIATRACK')

@section('content')
<!-- Modal Overlay with Blurred Background -->
<div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" x-data="{ roleId: '3' }">
    <div class="bg-white rounded-3xl border-2 border-slate-200 max-w-3xl w-full p-8 sm:p-10 space-y-8 shadow-2xl my-8 transform transition-all max-h-[90vh] overflow-y-auto custom-scrollbar">
        
        <!-- Header & Close/Back Button -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-[#8b1818] text-white flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-user-plus text-amber-300"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Add New User Account</h1>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">Enroll and register a new user into the directory.</p>
                </div>
            </div>

            <!-- Close Button (Redirects back to index) -->
            <a href="{{ route('admin.users.index') }}" 
               class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </a>
        </div>

        <!-- Validation Errors Banner -->
        @if($errors->any())
            <div class="p-4 bg-red-50 border-2 border-red-300 text-red-800 text-xs font-bold rounded-2xl flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-[#8b1818] text-base mt-0.5 shrink-0"></i>
                <div class="space-y-1">
                    <span class="font-extrabold block">Please correct the following errors:</span>
                    <ul class="list-disc pl-4 space-y-0.5 text-[11px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off" class="space-y-8">
            @csrf

            <!-- Account Role Type Selection -->
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-2">Account Role Type <span class="text-red-600">*</span></label>
                <select name="role_id" x-model="roleId" required class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-2xl focus:border-[#8b1818]">
                    <option value="3">Student</option>
                    <option value="2">Teacher</option>
                    <option value="4">Director</option>
                </select>
            </div>

            <!-- Personal Information -->
            <div class="space-y-4">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Last Name <span class="text-red-600">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">First Name <span class="text-red-600">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            <span x-text="roleId == '3' ? 'LRN / Student ID' : 'Employee ID'"></span> <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="id_number" value="{{ old('id_number') }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender <span class="text-red-600">*</span></label>
                        <select name="gender" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none">
                            <option value="" disabled selected>Select Gender</option>
                            <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Male</option>
                            <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address <span class="text-red-600">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Contact Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Parent / Guardian Name</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name') }}" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Parent Contact Number</label>
                        <input type="text" name="parent_phone_number" value="{{ old('parent_phone_number') }}" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                </div>
            </div>
            
            <!-- ================= STUDENT ONLY FIELDS ================= -->
            <div x-show="roleId == '3'" 
                 x-data="{ 
                     selectedGrade: '{{ old('grade_level', '') }}', 
                     selectedStrand: '{{ old('strand', '') }}',
                     sections: @js($sections),
                     get filteredStrands() {
                         if (!this.selectedGrade) return [];
                         let list = this.sections
                             .filter(s => s.grade_level && s.grade_level.trim().toLowerCase() === this.selectedGrade.trim().toLowerCase() && s.strand)
                             .map(s => s.strand.trim().toUpperCase());
                         return [...new Set(list)];
                     },
                     get filteredSections() {
                         if (!this.selectedGrade) return [];
                         return this.sections.filter(s => s.grade_level && s.grade_level.trim().toLowerCase() === this.selectedGrade.trim().toLowerCase());
                     }
                 }" 
                 class="space-y-4 pt-4 border-t border-slate-200">
                 
                <h3 class="text-sm font-black text-[#8b1818] uppercase tracking-wider">Student Academic Placement</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Grade Level Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Grade Level <span class="text-red-600">*</span></label>
                        <select name="grade_level" 
                                x-model="selectedGrade" 
                                @change="selectedStrand = ''"
                                required 
                                class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none">
                            <option value="" disabled selected>Select Grade Level</option>
                            @if(isset($gradeLevels) && count($gradeLevels) > 0)
                                @foreach($gradeLevels as $grade)
                                    <option value="{{ $grade }}" {{ old('grade_level') == $grade ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Strand / Track Selection (Filtered by Grade Level) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Strand / Track</label>
                        <select name="strand" 
                                x-model="selectedStrand"
                                class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none">
                            <option value="" disabled selected>Select Strand (Optional)</option>
                            <template x-for="strand in filteredStrands" :key="strand">
                                <option :value="strand" x-text="strand" :selected="strand === '{{ old('strand') }}'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Section Name Selection (Filtered by Grade Level) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Section <span class="text-red-600">*</span></label>
                        <select name="section" 
                                required 
                                class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none">
                            <option value="" disabled selected>Select Section</option>
                            <template x-for="sec in filteredSections" :key="sec.id">
                                <option :value="sec.section_name ?? sec.name" x-text="sec.section_name ?? sec.name" :selected="(sec.section_name ?? sec.name) === '{{ old('section') }}'"></option>
                            </template>
                        </select>
                    </div>

                </div>
            </div>

            <!-- PASSWORD SECTION -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Account Security Credentials</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Password (min. 8 characters) <span class="text-red-600">*</span></label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm Password <span class="text-red-600">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="8" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                </div>
            </div>

            <!-- FOOTER ACTIONS -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase transition border border-slate-200 flex items-center">Cancel</a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/20 cursor-pointer">Save User Account</button>
            </div>
        </form>
    </div>
</div>
@endsection