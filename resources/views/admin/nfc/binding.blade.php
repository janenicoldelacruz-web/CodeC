@extends('layouts.app')

@section('title', 'NFC Card Binding - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70" 
     x-data="{ 
         searchQuery: '{{ addslashes(request('student_name', '')) }}', 
         selectedStudent: '{{ request('student_id', '') }}', 
         tagId: '',
         showDuplicateModal: false,
         conflictDetails: {
             uid: '',
             studentName: '',
             lrn: ''
         },

         boundCardsList: [
             @foreach($boundCards as $card)
             {
                 tag_id: '{{ strtoupper(trim($card->tag_id)) }}',
                 user_id: '{{ $card->user_id }}',
                 student_name: '{{ addslashes(($card->user->last_name ?? '') . ', ' . ($card->user->first_name ?? '')) }}',
                 lrn: '{{ $card->user->id_number ?? 'Unassigned' }}'
             },
             @endforeach
         ],

         loadForRebind(studentId, fullName, lrn, currentTag) {
             this.selectedStudent = studentId;
             this.searchQuery = fullName + (lrn ? ' (' + lrn + ')' : '');
             this.tagId = currentTag;
             window.scrollTo({ top: 0, behavior: 'smooth' });
         },

         copyUid(uid) {
             navigator.clipboard.writeText(uid);
             alert('Card UID ' + uid + ' copied to clipboard!');
         },

         handleFormSubmit() {
             if (!this.selectedStudent) {
                 alert('Pumili muna ng estudyante mula sa listahan bago mag-save.');
                 return;
             }

             let cleanTag = this.tagId.trim().toUpperCase();
             if (!cleanTag) {
                 alert('Mag-tap muna ng NFC card o maglagay ng UID.');
                 return;
             }

             let duplicate = this.boundCardsList.find(c => c.tag_id === cleanTag && String(c.user_id) !== String(this.selectedStudent));

             if (duplicate) {
                 this.conflictDetails = {
                     uid: cleanTag,
                     studentName: duplicate.student_name,
                     lrn: duplicate.lrn
                 };
                 this.showDuplicateModal = true;
                 return;
             }

             this.$refs.bindingForm.submit();
         },

         initPolling() {
             setInterval(async () => {
                 try {
                     let response = await fetch('/api/nfc/latest');
                     let data = await response.json();
                     if (data.card_uid && this.tagId !== data.card_uid) {
                         this.tagId = data.card_uid;
                     }
                 } catch (e) {
                     // Polling retry
                 }
             }, 1000);
         }
     }" 
     x-init="initPolling()">

    <!-- Top Navigation & Status Header -->
    <header class="bg-white border-b border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                <i class="fa-solid fa-id-card-clip text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-slate-900 tracking-tight">NFC Card Assignment</h1>
                <p class="text-[11px] text-slate-500 font-bold">Link hardware identification credentials to verified student records</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 bg-emerald-50 text-emerald-800 text-[11px] font-black rounded-xl uppercase border border-emerald-200 shadow-2xs flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Reader Online & Ready</span>
            </span>
        </div>
    </header>

    <!-- Content Workspace -->
    <main class="pt-6 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-6 flex-1 max-w-5xl mx-auto">

        @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Side: Card Assignment Form Panel -->
            <div class="lg:col-span-7 bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-black shrink-0">
                        <i class="fa-solid fa-link"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Credential Assignment</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Select student record and capture physical card UID</p>
                    </div>
                </div>

                <form x-ref="bindingForm" action="{{ route('admin.nfc.binding') }}" method="POST" class="space-y-4" @submit.prevent="handleFormSubmit()">
                    @csrf
                    
                    <input type="hidden" name="user_id" :value="selectedStudent">

                    <!-- Searchable Student Selector -->
                    <div class="space-y-1.5" x-data="{ openDropdown: false }">
                        <label for="student_search_input" class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Student Account Identification</label>
                        
                        <div class="relative">
                            <input type="text" 
                                   id="student_search_input"
                                   name="student_search_input"
                                   x-model="searchQuery" 
                                   @focus="openDropdown = true" 
                                   placeholder="Search by full name or LRN / Student ID..." 
                                   autocomplete="off"
                                   required
                                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 pl-10 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                        </div>

                        <!-- Dropdown Search Menu -->
                        <div x-show="openDropdown && searchQuery.length > 0" @click.away="openDropdown = false" 
                             class="absolute z-30 mt-1 w-full bg-white border-2 border-slate-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100">
                            <template x-for="student in [
                                @foreach($users as $student)
                                    { id: '{{ $student->id }}', name: '{{ addslashes($student->last_name) }}, {{ addslashes($student->first_name) }}', lrn: '{{ $student->id_number ?? 'Unassigned ID' }}' },
                                @endforeach
                            ].filter(s => s.name.toLowerCase().includes(searchQuery.toLowerCase()) || s.lrn.toLowerCase().includes(searchQuery.toLowerCase()))">
                                <div @click="selectedStudent = student.id; searchQuery = student.name + ' (' + student.lrn + ')'; openDropdown = false;"
                                     class="px-4 py-3 hover:bg-slate-100 cursor-pointer transition flex items-center justify-between text-xs font-bold text-slate-800">
                                    <span x-text="student.name"></span>
                                    <span class="text-[10px] font-mono bg-slate-100 px-2 py-0.5 rounded-md text-slate-500" x-text="student.lrn"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- NFC Hardware Input Field -->
                    <div class="space-y-1.5">
                        <label for="nfc_tag_id_input" class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Hardware Card UID</label>
                        <div class="relative">
                            <input type="text" 
                                   id="nfc_tag_id_input"
                                   name="tag_id" 
                                   x-model="tagId"
                                   @keydown.enter.prevent=""
                                   placeholder="Awaiting scan or enter UID manually..." 
                                   required 
                                   autocomplete="off"
                                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 pl-10 text-xs font-bold font-mono text-slate-900 uppercase focus:outline-none focus:border-[#8b1818] transition tracking-wider">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-microchip text-xs"></i>
                            </div>
                        </div>
                        <p class="text-[9px] text-slate-400 font-bold">Position card over the terminal to register the unique identifier.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Confirm & Authorize Binding
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Active Registry List Panel -->
            <div class="lg:col-span-5 bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-4 flex flex-col">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-xs font-black shrink-0">
                        <i class="fa-solid fa-id-card text-amber-300"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Registry Records</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Active identification assignments</p>
                    </div>
                </div>

                <div class="space-y-2.5 overflow-y-auto max-h-[380px] pr-1 flex-1">
                    @forelse($boundCards as $card)
                    <div class="p-3.5 rounded-2xl bg-slate-50/70 border-2 border-slate-200 hover:border-slate-300 transition flex items-center justify-between gap-3">
                        <div class="space-y-0.5 overflow-hidden">
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="copyUid('{{ $card->tag_id }}')" 
                                        class="px-2 py-0.5 bg-slate-200/80 hover:bg-slate-300 text-slate-900 rounded-md text-[10px] font-mono font-black border border-slate-300 transition cursor-pointer flex items-center gap-1"
                                        title="Click to copy UID">
                                    <span>{{ $card->tag_id }}</span>
                                    <i class="fa-regular fa-copy text-[9px] text-slate-500"></i>
                                </button>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 truncate">
                                {{ $card->user->last_name ?? 'N/A' }}, {{ $card->user->first_name ?? '' }}
                            </h4>
                            <p class="text-[10px] text-slate-400 font-bold">
                                {{ $card->created_at ? $card->created_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>

                        <!-- Quick Re-bind / Edit Action -->
                        <button type="button" 
                                @click="loadForRebind('{{ $card->user_id }}', '{{ addslashes(($card->user->last_name ?? '') . ', ' . ($card->user->first_name ?? '')) }}', '{{ $card->user->id_number ?? '' }}', '{{ $card->tag_id }}')" 
                                class="px-3 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 transition flex items-center gap-1.5 text-xs font-bold border border-slate-200 shadow-2xs cursor-pointer" 
                                title="Load student into form to replace or re-assign card">
                            <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                            <span>Re-bind</span>
                        </button>
                    </div>
                    @empty
                    <div class="py-12 text-center text-slate-400 font-bold space-y-1 my-auto">
                        <div class="text-xl text-slate-300"><i class="fa-solid fa-id-card-clip"></i></div>
                        <p class="text-xs">No active credential records found.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

    <!-- Conflict Resolution Modal -->
    <div x-show="showDuplicateModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6" 
         style="display: none;"
         x-cloak>
        
        <div @click.outside="showDuplicateModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white rounded-3xl border border-slate-200 p-7 sm:p-8 max-w-md w-full shadow-2xl space-y-6 text-center relative">
            
            <button @click="showDuplicateModal = false" type="button" 
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="w-16 h-16 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center text-2xl mx-auto shadow-2xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-base font-black text-slate-900 tracking-tight">Card Assignment Conflict</h3>
                <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                    This NFC card is already registered to another active student profile.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-left">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Scanned UID</span>
                    <span class="px-2.5 py-0.5 bg-slate-200/70 text-slate-900 rounded-md text-[11px] font-mono font-black border border-slate-300/80 shadow-2xs" x-text="conflictDetails.uid"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Assigned Student</span>
                    <span class="text-xs font-black text-slate-900 truncate max-w-[200px]" x-text="conflictDetails.studentName"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Student ID / LRN</span>
                    <span class="text-xs font-mono font-bold text-slate-600" x-text="conflictDetails.lrn"></span>
                </div>
            </div>

            <div class="pt-1">
                <button @click="showDuplicateModal = false" type="button" 
                        class="w-full py-3.5 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-rotate-left"></i> Tap Another Card
                </button>
            </div>
        </div>
    </div>

</div>
@endsection