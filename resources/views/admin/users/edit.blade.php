@extends('layouts.app')

@section('title', 'Edit User - SIATRACK')

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<!-- Modal Overlay with Blurred Background -->
<div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 no-scrollbar" 
     x-data="{ 
        roleId: '{{ old('role_id', $user->role_id) }}',
        password: '',
        passwordConfirmation: '',
        showPassword: false,
        teacherType: '{{ old('teacher_type', ($user->role_id == 2 && $user->grade_level) ? 'Adviser' : '') }}',
        showSuccessModal: {{ session('success') ? 'true' : 'false' }}
     }">
    
    <!-- EDIT USER MODAL CONTAINER -->
    <div class="bg-white rounded-3xl border-2 border-slate-200 max-w-3xl w-full p-8 sm:p-10 space-y-8 shadow-2xl my-8 transform transition-all max-h-[90vh] overflow-y-auto no-scrollbar">
        
        <!-- Header & Close/Back Button -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-[#8b1818] text-white flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-user-pen text-amber-300"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit User Account</h1>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">Update user profile details and academic placement.</p>
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

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" autocomplete="off" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="space-y-4">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            <span x-text="roleId == '3' ? 'LRN / Student ID' : 'Employee ID'"></span>
                        </label>
                        <input type="text" name="id_number" value="{{ old('id_number', $user->id_number) }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Gender</label>
                        <select name="gender" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none">
                            <option value="" disabled>Select Gender</option>
                            <option value="1" {{ old('gender', $user->gender) == '1' ? 'selected' : '' }}>Male</option>
                            <option value="2" {{ old('gender', $user->gender) == '2' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Contact Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none font-mono">
                    </div>

                    <!-- Student Only Personal Fields (Parent Details) -->
                    <template x-if="roleId == '3'">
                        <div class="contents">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Parent / Guardian Name</label>
                                <input type="text" name="parent_name" value="{{ old('parent_name', $user->parent_name) }}" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Parent Contact Number</label>
                                <input type="text" name="parent_phone_number" value="{{ old('parent_phone_number', $user->parent_phone_number) }}" class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl focus:border-[#8b1818] outline-none font-mono">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================= TEACHER ONLY FIELDS ================= -->
            <div x-show="roleId == '2'" 
                 x-data="{
                     advisoryGrade: '{{ old('grade_level', $user->grade_level) }}',
                     sections: @js($sections),
                     get advisorySections() {
                         if (!this.advisoryGrade) return [];
                         return this.sections.filter(s => s.grade_level && s.grade_level.trim().toLowerCase() === this.advisoryGrade.trim().toLowerCase());
                     }
                 }"
                 class="space-y-6 pt-5 border-t border-slate-200">
                 
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8b1818]"></span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500">Faculty Role Assignment</h3>
                </div>

                <!-- Main Teacher Designation Selector with Default Blank Option -->
                <div class="bg-slate-50/70 border border-slate-200 rounded-2xl p-5 space-y-2.5 shadow-xs">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Teacher Designation</label>
                    <div class="relative">
                        <select name="teacher_type" 
                                x-model="teacherType"
                                :disabled="roleId != '2'"
                                :required="roleId == '2'"
                                class="w-full px-4 py-3.5 text-sm font-bold text-slate-900 border-2 border-slate-300 rounded-xl bg-white focus:border-[#8b1818] focus:ring-4 focus:ring-[#8b1818]/10 outline-none transition appearance-none cursor-pointer">
                            <option value="" disabled selected>Select Designation</option>
                            <option value="Subject Teacher">Subject Teacher</option>
                            <option value="Adviser">Adviser</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Advisory Class Assignment -->
                <div x-show="teacherType === 'Adviser'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="p-6 bg-red-50/50 border-2 border-red-200/80 rounded-2xl space-y-4 shadow-sm">
                    
                    <div class="flex items-center gap-2 pb-1 border-b border-red-200/60">
                        <span class="w-2 h-2 rounded-full bg-[#8b1818]"></span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#8b1818]">Advisory Class Placement</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Advisory Grade Level</label>
                            <select name="grade_level" 
                                    x-model="advisoryGrade"
                                    :disabled="roleId != '2' || teacherType !== 'Adviser'"
                                    :required="roleId == '2' && teacherType === 'Adviser'"
                                    class="w-full px-4 py-3.5 text-sm font-bold text-slate-900 border-2 border-slate-300 rounded-xl bg-white focus:border-[#8b1818] focus:ring-4 focus:ring-[#8b1818]/10 outline-none transition cursor-pointer">
                                <option value="" disabled selected>Select Grade Level</option>
                                @if(isset($gradeLevels) && count($gradeLevels) > 0)
                                    @foreach($gradeLevels as $grade)
                                        <option value="{{ $grade }}" {{ old('grade_level', $user->grade_level) == $grade ? 'selected' : '' }}>
                                            {{ $grade }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Advisory Section</label>
                            <select name="section" 
                                    :disabled="roleId != '2' || teacherType !== 'Adviser'"
                                    :required="roleId == '2' && teacherType === 'Adviser'"
                                    class="w-full px-4 py-3.5 text-sm font-bold text-slate-900 border-2 border-slate-300 rounded-xl bg-white focus:border-[#8b1818] focus:ring-4 focus:ring-[#8b1818]/10 outline-none transition cursor-pointer">
                                <option value="" disabled selected>Select Section</option>
                                <template x-for="sec in advisorySections" :key="sec.id">
                                    <option :value="sec.section_name ?? sec.name" x-text="sec.section_name ?? sec.name" :selected="(sec.section_name ?? sec.name) === '{{ old('section', $user->section) }}'"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
           <!-- ================= STUDENT ONLY FIELDS ================= -->
<div x-show="roleId == '3'" class="space-y-4 pt-4 border-t border-slate-200">
    <h3 class="text-sm font-black text-[#8b1818] uppercase tracking-wider">Student Academic Placement</h3>
    
    @php
        // Kunin ang mga unique strands mula sa sections para sa dropdown
        $allStrands = $sections->pluck('strand')->filter()->map(fn($s) => strtoupper(trim($s)))->unique()->values();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Grade Level Selection -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Grade Level</label>
            <select name="grade_level" 
                    class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none">
                <option value="">Select Grade Level</option>
                @if(isset($gradeLevels) && count($gradeLevels) > 0)
                    @foreach($gradeLevels as $grade)
                        <option value="{{ $grade }}" {{ old('grade_level', $user->grade_level) == $grade ? 'selected' : '' }}>
                            {{ $grade }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Strand / Track Selection (Gumamit ng Blade foreach para sigurado) -->
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Strand / Track</label>
            <select name="strand" 
                    class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none uppercase">
                <option value="">Select Strand (Optional)</option>
                @foreach($allStrands as $str)
                    <option value="{{ $str }}" {{ old('strand', $user->strand ?? $user->track) == $str ? 'selected' : '' }}>
                        {{ $str }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Section Name Selection -->
<div>
    <label class="block text-xs font-bold text-slate-700 mb-1.5">Section</label>
    <select name="section" 
            class="w-full px-4 py-3 text-sm font-semibold border border-slate-300 rounded-xl bg-white text-slate-700 focus:border-[#8b1818] outline-none uppercase">
        <option value="">Select Section</option>
        @foreach($sections as $sec)
            <!-- Ang value at text ay section_name lang -->
            <option value="{{ $sec->section_name }}" {{ old('section', $user->section) == $sec->section_name ? 'selected' : '' }}>
                {{ $sec->section_name }}
            </option>
        @endforeach
    </select>
</div>

    </div>
</div>

            
<!-- PASSWORD SECTION -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
                        Account Security Credentials
                    </h3>
                    <button type="button" @click="showPassword = !showPassword"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition cursor-pointer">
                        <i class="fa-solid text-xs" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        <span x-text="showPassword ? 'Hide Passwords' : 'Show Passwords'"></span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Current Password</label>
                        <input :type="showPassword ? 'text' : 'password'" readonly value="{{ $user->password }}" class="w-full px-4 py-3 text-sm font-semibold bg-slate-100 border border-slate-300 rounded-xl text-slate-800 font-mono cursor-not-allowed" title="Current password">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">New Password</label>
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" placeholder="Leave blank to keep"
                            class="w-full px-4 py-3 text-sm font-mono font-bold border border-slate-300 rounded-xl focus:border-[#8b1818] bg-slate-50/50 focus:bg-white outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Confirm New Password</label>
                        <input :type="showPassword ? 'text' : 'password'" name="password_confirmation"
                            x-model="passwordConfirmation" placeholder="Leave blank to keep"
                            class="w-full px-4 py-3 text-sm font-mono font-bold border border-slate-300 rounded-xl focus:border-[#8b1818] bg-slate-50/50 focus:bg-white outline-none transition">
                    </div>
                </div>
            </div>

            <!-- FOOTER ACTIONS -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase transition border border-slate-200 flex items-center">Cancel</a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/20 cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- SUCCESS MODAL POPUP -->
    <div x-show="showSuccessModal" 
         class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition.opacity>
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl border border-slate-100">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl shadow-inner">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-black text-slate-900">Successfully Updated!</h3>
                <p class="text-xs text-slate-500 font-medium">{{ session('success') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" 
               class="w-full py-3 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider block transition shadow-md shadow-red-950/20">
                Okay
            </a>
        </div>
    </div>
</div>
@endsection