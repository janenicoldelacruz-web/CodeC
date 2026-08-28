@extends('layouts.app')

@section('title', (isset($user) ? 'Edit User' : 'Add New User') . ' - SIATRACK')

@section('content')
<div class="w-full min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-slate-50 flex items-center justify-center">

    <!-- Form Container -->
    <div class="max-w-4xl w-full bg-white rounded-3xl border-2 border-slate-200 p-8 sm:p-10 space-y-8 my-4">
        
        <!-- Header & Back Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-[#8b1818] text-white flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid {{ isset($user) ? 'fa-user-pen' : 'fa-user-plus' }} text-amber-300"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        {{ isset($user) ? 'Edit User Account' : 'Add New User Account' }}
                    </h1>
                    <p class="text-xs font-bold text-slate-500 mt-0.5">
                        {{ isset($user) ? 'Update profile information, academic placement, credentials, and access permissions.' : 'Enroll and register a new student or faculty member into the directory.' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-white text-slate-700 text-xs font-black border-2 border-slate-200 shrink-0">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Users</span>
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

        <form method="POST" 
              action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" 
              enctype="multipart/form-data"
              autocomplete="off" 
              class="space-y-8">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <!-- Role Selection -->
            <div>
                <label for="role_select" class="block text-sm font-bold text-slate-900 mb-2">
                    Account Role Type <span class="text-red-600">*</span>
                </label>
                <div class="relative">
                    <select name="role_id" 
                            id="role_select" 
                            onchange="toggleStudentFields()" 
                            required
                            class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818] appearance-none cursor-pointer">
                        @foreach($roles as $role)
                            @if(in_array(strtolower($role->name), ['teacher', 'faculty', 'student']) || in_array($role->id, [2, 3]))
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? 3) == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- ================= SECTION 1: PERSONAL INFORMATION ================= -->
            <div class="space-y-6">
                <div class="border-b border-slate-100 pb-2">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">Personal Information</h2>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">Enter the user's basic credentials and contact details.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-bold text-slate-900 mb-2">
                            Last Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               id="last_name"
                               name="last_name" 
                               value="{{ old('last_name', $user->last_name ?? '') }}" 
                               required 
                               placeholder="Last Name" 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 2. First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-bold text-slate-900 mb-2">
                            First Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               id="first_name"
                               name="first_name" 
                               value="{{ old('first_name', $user->first_name ?? '') }}" 
                               required 
                               placeholder="First Name" 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 3. LRN / Employee ID -->
                    <div>
                        <label id="id_number_label" for="id_number" class="block text-sm font-bold text-slate-900 mb-2">
                            LRN <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               id="id_number"
                               name="id_number" 
                               value="{{ old('id_number', $user->id_number ?? '') }}" 
                               placeholder="103677130052" 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-semibold font-mono text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 4. Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-bold text-slate-900 mb-2">
                            Gender <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <select id="gender" 
                                    name="gender" 
                                    required
                                    class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818] appearance-none cursor-pointer">
                                <option value="" disabled {{ !isset($user) || empty($user->gender) ? 'selected' : '' }}>Select Gender</option>
                                <option value="1" {{ (string)old('gender', $user->gender ?? '') === '1' ? 'selected' : '' }}>Male</option>
                                <option value="2" {{ (string)old('gender', $user->gender ?? '') === '2' ? 'selected' : '' }}>Female</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>  

                    <!-- 5. Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-900 mb-2">
                            Email Address <span class="text-red-600">*</span>
                        </label>
                        <input type="email" 
                               id="email"
                               name="email" 
                               value="{{ old('email', $user->email ?? '') }}" 
                               required 
                               placeholder="Email Address" 
                               autocomplete="new-password"
                               class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 6. Contact Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-bold text-slate-900 mb-2">
                            Contact Number
                        </label>
                        <input type="text" 
                               id="phone_number"
                               name="phone_number" 
                               value="{{ old('phone_number', $user->phone_number ?? '') }}" 
                               placeholder="Phone Number" 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 7. Parent / Guardian Name (Student Only) -->
                    <div id="parent_name_container">
                        <label for="parent_name" class="block text-sm font-bold text-slate-900 mb-2">
                            Parent / Guardian Name
                        </label>
                        <input type="text" 
                               id="parent_name"
                               name="parent_name" 
                               value="{{ old('parent_name', $user->parent_name ?? '') }}" 
                               placeholder="Parent Name" 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 8. Parent Contact Number (Student Only) -->
                    <div id="parent_contact_container">
                        <label for="parent_phone_number" class="block text-sm font-bold text-slate-900 mb-2">
                            Parent Contact Number
                        </label>
                        <input type="text" 
                               id="parent_phone_number"
                               name="parent_phone_number" 
                               value="{{ old('parent_phone_number', $user->parent_phone_number ?? '') }}" 
                               placeholder="Parent Contact Number" 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                    </div>

                    <!-- 9. NFC Tag Card Assignment Container (Student Only) -->
                    <div id="nfc_container" class="p-5 border-2 border-red-200 rounded-2xl bg-red-50/20 space-y-3 md:col-span-2">
                        <div class="flex items-center justify-between">
                            <label for="nfc_input_field" class="block text-sm font-bold text-slate-900">
                                NFC Tag ID (Card Assignment)
                            </label>
                            <span id="nfc_status_badge" class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">
                                Live Reader Listening
                            </span>
                        </div>
                        <input type="text" 
                               id="nfc_input_field" 
                               name="nfc_tag_id" 
                               value="{{ old('nfc_tag_id', isset($user->nfcCard) ? $user->nfcCard->tag_id : '') }}" 
                               placeholder="Tap card on ACR122U reader..." 
                               autocomplete="off"
                               class="w-full px-4 py-3.5 text-sm font-mono font-bold uppercase text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                        
                        <div id="nfc_duplicate_alert" class="hidden p-3 bg-red-100 border border-red-300 rounded-xl text-red-800 text-xs font-bold flex items-center gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-red-600 text-base shrink-0"></i>
                            <span id="nfc_duplicate_text">Warning: This NFC card is already registered to another user.</span>
                        </div>

                        <p id="nfc_helper_text" class="text-xs text-slate-500 font-medium">Hold the student's NFC card near the ACR122U reader to automatically capture the UID.</p>
                    </div>
                </div>
            </div>

            <!-- ================= SECTION 2: ACADEMIC INFO, PHOTO & PASSWORD ================= -->
            <div id="academic_and_password_section" class="space-y-6 pt-2">
                <div class="border-b border-slate-100 pb-2">
                    <h2 id="section_2_title" class="text-lg font-black text-slate-900 tracking-tight">Academic Placement & Security</h2>
                    <p id="section_2_subtitle" class="text-xs font-semibold text-slate-500 mt-0.5">Assign educational placement details and set up account password credentials.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Grade Level (Student Only: 11, 12) -->
                    <div id="grade_level_container">
                        <label for="grade_level" class="block text-sm font-bold text-slate-900 mb-2">Grade Level</label>
                        <div class="relative">
                            <select id="grade_level" 
                                    name="grade_level" 
                                    class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818] appearance-none cursor-pointer">
                                <option value="">Select Grade Level</option>
                                <option value="11" {{ (int)old('grade_level', $user->grade_level ?? 0) === 11 ? 'selected' : '' }}>Grade 11</option>
                                <option value="12" {{ (int)old('grade_level', $user->grade_level ?? 0) === 12 ? 'selected' : '' }}>Grade 12</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Track (Student Only: 1 = Academic Track, 2 = Technical-Professional) -->
                    <div id="track_container">
                        <label for="track" class="block text-sm font-bold text-slate-900 mb-2">Track</label>
                        <div class="relative">
                            <select id="track" 
                                    name="track" 
                                    class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818] appearance-none cursor-pointer">
                                <option value="">Select Track</option>
                                <option value="1" {{ (int)old('track', $user->track ?? ($user->strand ?? 0)) === 1 ? 'selected' : '' }}>Academic Track</option>
                                <option value="2" {{ (int)old('track', $user->track ?? ($user->strand ?? 0)) === 2 ? 'selected' : '' }}>Technical-Professional</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Section (Student Only: 1 = Amber, 2 = Crystal, 3 = Pearl, 4 = Turquoise) -->
                    <div id="section_container" class="md:col-span-2">
                        <label for="section" class="block text-sm font-bold text-slate-900 mb-2">Section</label>
                        <div class="relative">
                            <select id="section" 
                                    name="section" 
                                    class="w-full px-4 py-3.5 text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818] appearance-none cursor-pointer">
                                <option value="">Select Section</option>
                                <option value="1" {{ (int)old('section', $user->section ?? 0) === 1 ? 'selected' : '' }}>Amber</option>
                                <option value="2" {{ (int)old('section', $user->section ?? 0) === 2 ? 'selected' : '' }}>Crystal</option>
                                <option value="3" {{ (int)old('section', $user->section ?? 0) === 3 ? 'selected' : '' }}>Pearl</option>
                                <option value="4" {{ (int)old('section', $user->section ?? 0) === 4 ? 'selected' : '' }}>Turquoise</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty Photo Upload (Positioned right before password) -->
                    <div id="photo_container" class="md:col-span-2 p-5 border-2 border-slate-200 rounded-2xl bg-slate-50 space-y-3">
                        <label for="photo" class="block text-sm font-bold text-slate-900">
                            Faculty Profile Photo <span class="text-xs text-slate-400 font-normal">(Optional, JPG/PNG up to 2MB)</span>
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-slate-200 border-2 border-slate-300 overflow-hidden flex items-center justify-center shrink-0">
                                @if(isset($user) && $user->photo)
                                    <img id="photo_preview" src="{{ asset('storage/' . $user->photo) }}" alt="Preview" class="w-full h-full object-cover">
                                @else
                                    <img id="photo_preview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                                    <i id="photo_placeholder" class="fa-solid fa-camera text-slate-400 text-xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       id="photo" 
                                       name="photo" 
                                       accept="image/jpeg,image/png,image/jpg,image/webp"
                                       onchange="previewProfilePhoto(this)"
                                       class="w-full text-xs font-semibold text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#8b1818] file:text-white hover:file:bg-[#731414] cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div id="password_container">
                        <label for="password_input" class="block text-sm font-bold text-slate-900 mb-2">
                            {{ isset($user) ? 'Account Password' : 'Password Setup' }}
                            <span class="text-xs text-slate-400 font-normal">(min. 8 characters)</span>
                        </label>
                        <div class="relative flex items-center">
                            <input type="password" 
                                   id="password_input" 
                                   name="password" 
                                   minlength="8" 
                                   {{ !isset($user) ? 'required' : '' }}
                                   value="{{ isset($user) ? '••••••••' : '' }}" 
                                   onfocus="if(this.value==='••••••••') this.value='';" 
                                   onblur="if(this.value==='') this.value='{{ isset($user) ? '••••••••' : '' }}';"
                                   placeholder="Type new password..." 
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3.5 pr-11 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                            
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_input', 'eye_icon_1')" 
                                    class="absolute right-4 text-slate-400 hover:text-slate-700 transition focus:outline-none cursor-pointer">
                                <i id="eye_icon_1" class="fa-solid fa-eye text-base"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div id="confirm_password_container">
                        <label for="password_confirm_input" class="block text-sm font-bold text-slate-900 mb-2">
                            Confirm Password
                            <span class="text-xs text-slate-400 font-normal">(min. 8 characters)</span>
                        </label>
                        <div class="relative flex items-center">
                            <input type="password" 
                                   id="password_confirm_input" 
                                   name="password_confirmation" 
                                   minlength="8" 
                                   {{ !isset($user) ? 'required' : '' }}
                                   value="{{ isset($user) ? '••••••••' : '' }}" 
                                   onfocus="if(this.value==='••••••••') this.value='';" 
                                   onblur="if(this.value==='') this.value='{{ isset($user) ? '••••••••' : '' }}';"
                                   placeholder="Re-type new password..." 
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3.5 pr-11 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-2xl focus:outline-none focus:border-[#8b1818]">
                            
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_confirm_input', 'eye_icon_2')" 
                                    class="absolute right-4 text-slate-400 hover:text-slate-700 transition focus:outline-none cursor-pointer">
                                <i id="eye_icon_2" class="fa-solid fa-eye text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons Aligned Right -->
            <div class="w-full flex justify-end items-center gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-8 py-3.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase tracking-wider transition border border-slate-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-8 py-3.5 rounded-2xl bg-[#8b1818] hover:bg-[#731414] text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/20 active:scale-[0.98] border-b-4 border-[#5e0f0f] cursor-pointer">
                    {{ isset($user) ? 'Update User' : 'Save User Account' }}
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleStudentFields() {
        const roleSelect = document.getElementById('role_select');
        if (!roleSelect) return;
        const selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
        const isStudent = selectedText.includes('student');

        const photoContainer = document.getElementById('photo_container');
        const nfcContainer = document.getElementById('nfc_container');
        const parentNameContainer = document.getElementById('parent_name_container');
        const parentContactContainer = document.getElementById('parent_contact_container');
        const gradeLevelContainer = document.getElementById('grade_level_container');
        const trackContainer = document.getElementById('track_container');
        const sectionContainer = document.getElementById('section_container');

        const sectionTitle = document.getElementById('section_2_title');
        const sectionSubtitle = document.getElementById('section_2_subtitle');
        const idLabel = document.getElementById('id_number_label');

        if (isStudent) {
            if (photoContainer) photoContainer.style.display = 'none';
            if (idLabel) idLabel.innerHTML = 'LRN <span class="text-red-600">*</span>';
            if (nfcContainer) nfcContainer.style.display = 'block';
            if (parentNameContainer) parentNameContainer.style.display = 'block';
            if (parentContactContainer) parentContactContainer.style.display = 'block';
            if (gradeLevelContainer) gradeLevelContainer.style.display = 'block';
            if (trackContainer) trackContainer.style.display = 'block';
            if (sectionContainer) sectionContainer.style.display = 'block';

            if (sectionTitle) sectionTitle.innerText = 'Academic Placement & Security';
            if (sectionSubtitle) sectionSubtitle.innerText = 'Assign grade level, track, section, and configure access password.';
        } else {
            if (photoContainer) photoContainer.style.display = 'block';
            if (idLabel) idLabel.innerHTML = 'Faculty / Employee ID <span class="text-red-600">*</span>';
            if (nfcContainer) nfcContainer.style.display = 'none';
            if (parentNameContainer) parentNameContainer.style.display = 'none';
            if (parentContactContainer) parentContactContainer.style.display = 'none';
            if (gradeLevelContainer) gradeLevelContainer.style.display = 'none';
            if (trackContainer) trackContainer.style.display = 'none';
            if (sectionContainer) sectionContainer.style.display = 'none';

            if (sectionTitle) sectionTitle.innerText = 'Faculty Account Security & Photo';
            if (sectionSubtitle) sectionSubtitle.innerText = 'Upload faculty profile photo and set login password.';
        }
    }

    function previewProfilePhoto(input) {
        const preview = document.getElementById('photo_preview');
        const placeholder = document.getElementById('photo_placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function pollLatestNfcTap() {
        const nfcInput = document.getElementById('nfc_input_field');
        const badge = document.getElementById('nfc_status_badge');
        const alertBox = document.getElementById('nfc_duplicate_alert');
        const alertText = document.getElementById('nfc_duplicate_text');
        const helperText = document.getElementById('nfc_helper_text');
        const currentUserId = '{{ isset($user) ? $user->id : "" }}';

        fetch('/api/nfc/latest-tap?current_user_id=' + currentUserId + '&t=' + Date.now(), {
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.tag_id && data.tag_id.trim() !== '') {
                const tag = data.tag_id.trim();
                if (nfcInput && nfcInput.value !== tag) {
                    nfcInput.value = tag;

                    if (data.is_registered) {
                        nfcInput.classList.remove('bg-emerald-50', 'border-emerald-500');
                        nfcInput.classList.add('bg-red-50', 'border-red-500', 'text-red-700');

                        if (badge) {
                            badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Already Registered!';
                            badge.className = 'text-xs font-bold text-white bg-red-600 px-3 py-1 rounded-full';
                        }

                        if (alertBox && alertText) {
                            alertText.innerText = 'Warning: NFC Card [' + tag + '] is already assigned to ' + (data.owner_name || 'another user') + '.';
                            alertBox.classList.remove('hidden');
                        }
                        if (helperText) helperText.classList.add('hidden');
                    } else {
                        nfcInput.classList.remove('bg-red-50', 'border-red-500', 'text-red-700');
                        nfcInput.classList.add('bg-emerald-50', 'border-emerald-500', 'text-gray-900');

                        if (badge) {
                            badge.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i> Available: ' + tag;
                            badge.className = 'text-xs font-bold text-white bg-emerald-600 px-3 py-1 rounded-full';
                        }

                        if (alertBox) alertBox.classList.add('hidden');
                        if (helperText) helperText.classList.remove('hidden');
                    }
                }
            }
        })
        .catch(() => {});
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleStudentFields();
        setInterval(pollLatestNfcTap, 1000);
    });
</script>
@endpush