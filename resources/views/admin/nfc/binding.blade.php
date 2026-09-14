@extends('layouts.app')

@section('title', 'NFC Card Binding - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70 relative" 
     x-data="{ 
         searchQuery: '{{ addslashes(request('student_name', '')) }}', 
         selectedStudent: '{{ request('student_id', '') }}', 
         tagId: (function() {
             let selId = '{{ request('student_id', '') }}';
             if (!selId) return '';
             @foreach($users as $st)
                 if (String('{{ $st->id }}') === String(selId)) {
                     return '{{ $st->nfcCard ? strtoupper(trim($st->nfcCard->tag_id)) : '' }}';
                 }
             @endforeach
             return '';
         })(),
         showDuplicateModal: false,
         showLockedModal: false,
         openDropdown: false,
         registrySearch: '',
         conflictDetails: {
             uid: '',
             studentName: '',
             lrn: ''
         },
         
         // Toast / Success Modal State
         showToast: {{ session('success') || session('duplicate_error') || session('error') ? 'true' : 'false' }},
         toastMessage: '{{ session('success') ?? session('duplicate_error') ?? session('error') ?? '' }}',
         toastType: '{{ session('duplicate_error') || session('error') ? 'error' : 'success' }}',

         allStudents: [
             @foreach($users as $student)
                 { 
                     id: '{{ $student->id }}', 
                     name: '{{ addslashes($student->last_name) }}, {{ addslashes($student->first_name) }}', 
                     lrn: '{{ $student->id_number ?? 'Unassigned ID' }}',
                     hasCard: {{ $student->nfcCard ? 'true' : 'false' }},
                     cardUid: '{{ $student->nfcCard ? strtoupper(trim($student->nfcCard->tag_id)) : '' }}'
                 },
             @endforeach
         ],

         get filteredStudents() {
             if (!this.searchQuery) return this.allStudents;
             let query = this.searchQuery.toLowerCase();
             return this.allStudents.filter(s => s.name.toLowerCase().includes(query) || s.lrn.toLowerCase().includes(query));
         },

         boundCardsList: [
             @foreach($boundCards as $card)
             {
                 tag_id: '{{ strtoupper(trim($card->tag_id)) }}',
                 user_id: '{{ $card->user_id }}',
                 student_name: '{{ addslashes(($card->user->last_name ?? '') . ', ' . ($card->user->first_name ?? '')) }}',
                 lrn: '{{ $card->user->id_number ?? 'Unassigned' }}',
                 date: '{{ $card->created_at ? $card->created_at->format('M d, Y') : 'N/A' }}'
             },
             @endforeach
         ],

         get filteredBoundCards() {
             if (!this.registrySearch) return this.boundCardsList;
             let query = this.registrySearch.toLowerCase();
             return this.boundCardsList.filter(c => 
                 c.student_name.toLowerCase().includes(query) || 
                 c.lrn.toLowerCase().includes(query) || 
                 c.tag_id.toLowerCase().includes(query)
             );
         },

         get existingCardForStudent() {
             if (!this.selectedStudent) return null;
             
             let boundMatch = this.boundCardsList.find(c => String(c.user_id) === String(this.selectedStudent));
             if (boundMatch) {
                 return boundMatch;
             }

             let foundStudent = this.allStudents.find(s => String(s.id) === String(this.selectedStudent));
             if (foundStudent && foundStudent.hasCard) {
                 return { tag_id: foundStudent.cardUid };
             }

             return null;
         },

         selectStudent(student) {
             this.selectedStudent = student.id;
             this.searchQuery = student.name + ' (' + student.lrn + ')';
             this.openDropdown = false;
             
             if (student.hasCard) {
                 this.tagId = student.cardUid;
             } else {
                 this.tagId = '';
             }
         },

         clearSelection() {
             this.searchQuery = '';
             this.selectedStudent = '';
             this.tagId = '';
             this.openDropdown = false;
         },

         copyUid(uid) {
             navigator.clipboard.writeText(uid);
             alert('Card UID ' + uid + ' copied to clipboard!');
         },

         handleFormSubmit() {
             if (!this.selectedStudent) {
                 alert('Please select a student from the list before saving.');
                 return;
             }

             let selectedObj = this.allStudents.find(s => String(s.id) === String(this.selectedStudent));
             if (selectedObj && selectedObj.hasCard) {
                 this.showLockedModal = true;
                 return;
             }

             let cleanTag = this.tagId.trim().toUpperCase();
             if (!cleanTag) {
                 alert('Please tap an NFC card or enter a UID.');
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
                 let selectedObj = this.allStudents.find(s => String(s.id) === String(this.selectedStudent));
                 if (selectedObj && selectedObj.hasCard) return;

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

    <!-- Exactly Centered Success / Alert Modal Popup -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-md p-4"
         style="display: none;"
         x-cloak>
        <div class="bg-white border border-slate-200 shadow-2xl rounded-3xl p-7 max-w-sm w-full text-center relative space-y-5 animate-in fade-in zoom-in-95 duration-200">
            
            <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center text-white shadow-md text-xl"
                 :class="toastType === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
                <i class="fa-solid" :class="toastType === 'success' ? 'fa-check' : 'fa-triangle-exclamation'"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-base font-black text-slate-900 tracking-tight" x-text="toastType === 'success' ? 'Successfully Bound!' : 'Notice'"></h3>
                <p class="text-xs font-semibold text-slate-600 leading-relaxed" x-text="toastMessage"></p>
            </div>

            <div>
                <button @click="showToast = false" type="button" 
                        class="w-full py-3.5 rounded-xl text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer"
                        :class="toastType === 'success' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'">
                    Okay
                </button>
            </div>
        </div>
    </div>

    <!-- LOCKED STUDENT CARD WARNING MODAL -->
    <div x-show="showLockedModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 sm:p-6" 
         style="display: none;"
         x-cloak>
        
        <div @click.outside="showLockedModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white rounded-3xl border border-slate-200 p-7 sm:p-8 max-w-md w-full shadow-2xl space-y-6 text-center relative">
            
            <button @click="showLockedModal = false" type="button" 
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition flex items-center justify-center text-xs cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-2xl mx-auto shadow-2xs">
                <i class="fa-solid fa-lock"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-base font-black text-slate-900 tracking-tight">Active Card Already Assigned</h3>
                <p class="text-xs text-slate-500 font-semibold leading-relaxed">
                    This student already has an active NFC card. Please use the <strong class="text-slate-800">Replacement page</strong> if you wish to replace or update it.
                </p>
            </div>

            <div class="pt-1">
                <button @click="showLockedModal = false" type="button" 
                        class="w-full py-3.5 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> Understood
                </button>
            </div>
        </div>
    </div>

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
                    <div class="space-y-1.5 relative">
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
                                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 pl-10 pr-10 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>

                            <!-- Clear Search Query Button ("X") -->
                            <template x-if="searchQuery.length > 0">
                                <button type="button" 
                                        @click="clearSelection()"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </template>
                        </div>

                        <!-- Dropdown Search Menu -->
                        <div x-show="openDropdown && searchQuery.length > 0" 
                             @click.away="openDropdown = false" 
                             class="absolute left-0 right-0 z-30 mt-1 bg-white border-2 border-slate-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto divide-y divide-slate-100"
                             style="display: none;"
                             x-cloak>
                            
                            <!-- Results Loop -->
                            <template x-for="student in filteredStudents" :key="student.id">
                                <div @click="selectStudent(student)"
                                     class="px-4 py-3 hover:bg-slate-100 cursor-pointer transition flex items-center justify-between text-xs font-bold text-slate-800">
                                    <div class="flex items-center gap-2">
                                        <span x-text="student.name"></span>    
                                    </div>
                                    <span class="text-[10px] font-mono bg-slate-100 px-2 py-0.5 rounded-md text-slate-500" x-text="student.lrn"></span>
                                </div>
                            </template>

                            <!-- Dropdown Empty State -->
                            <template x-if="filteredStudents.length === 0">
                                <div class="px-4 py-6 text-center text-xs text-slate-400 font-bold space-y-1">
                                    <i class="fa-solid fa-circle-exclamation text-sm text-slate-300"></i>
                                    <p>No student records found matching your search.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Visual Feedback & Note if student already has a card bound -->
                    <template x-if="existingCardForStudent">
                        <div class="p-3.5 bg-amber-50 border-2 border-amber-200 rounded-xl flex items-start gap-3 text-amber-900 text-xs">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm mt-0.5 shrink-0"></i>
                            <div class="space-y-1">
                                <span class="font-black block uppercase tracking-wider text-[10px]">Existing NFC Card Detected</span>
                                <p class="text-[11px] font-medium leading-relaxed">
                                    This student already has an assigned NFC card UID: <strong class="font-mono underline" x-text="existingCardForStudent.tag_id"></strong>. This field is locked and cannot be changed here. (Use the Replacement page if you wish to change it).
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- NFC Hardware Input Field (Locked/Readonly if student has existing card) -->
                    <div class="space-y-1.5">
                        <label for="nfc_tag_id_input" class="block text-[10px] font-black uppercase tracking-wider text-slate-400">Hardware Card UID</label>
                        <div class="relative">
                            <input type="text" 
                                   id="nfc_tag_id_input"
                                   name="tag_id" 
                                   x-model="tagId"
                                   :readonly="existingCardForStudent !== null"
                                   @keydown.enter.prevent=""
                                   placeholder="Awaiting scan or enter UID manually..." 
                                   required 
                                   autocomplete="off"
                                   class="w-full border-2 rounded-xl px-3.5 py-2.5 pl-10 text-xs font-bold font-mono uppercase transition tracking-wider"
                                   :class="existingCardForStudent !== null ? 'bg-slate-100 border-slate-300 text-slate-500 cursor-not-allowed select-none' : 'bg-slate-50 border-slate-200 text-slate-900 focus:outline-none focus:border-[#8b1818]'">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-microchip text-xs"></i>
                            </div>
                        </div>
                        <p class="text-[9px] font-bold" :class="existingCardForStudent !== null ? 'text-amber-700' : 'text-slate-400'" 
                           x-text="existingCardForStudent !== null ? 'Card ID is locked because this student already has an active assignment.' : 'Position card over the terminal to register the unique identifier.'"></p>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3 rounded-xl text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2"
                                :class="existingCardForStudent !== null ? 'bg-slate-400 hover:bg-slate-500 cursor-not-allowed' : 'bg-[#8b1818] hover:bg-[#6b1212]'">
                            <i class="fa-solid fa-floppy-disk"></i> 
                            <span x-text="existingCardForStudent !== null ? 'Card Already Bound (Locked)' : 'Confirm & Authorize Binding'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Enhanced Active Registry List Panel with Live Count Badge -->
            <div class="lg:col-span-5 bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-4 flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-xs font-black shrink-0">
                            <i class="fa-solid fa-id-card text-amber-300"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Registry Records</h3>
                            <p class="text-[10px] text-slate-500 font-bold">Active identification assignments</p>
                        </div>
                    </div>
                    <!-- Live Record Counter Badge -->
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-mono font-black rounded-xl border border-slate-200" x-text="filteredBoundCards.length + ' Active'"></span>
                </div>

                <!-- Registry Search Filter Input -->
                <div class="relative">
                    <input type="text" 
                           x-model="registrySearch" 
                           placeholder="Filter registry records..." 
                           autocomplete="off"
                           class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2 pl-9 pr-8 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8b1818] transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-filter text-xs"></i>
                    </div>
                    <template x-if="registrySearch.length > 0">
                        <button type="button" 
                                @click="registrySearch = ''"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </template>
                </div>

                <!-- Registry List Cards -->
                <div class="space-y-3 overflow-y-auto max-h-[340px] pr-1 flex-1">
                    <template x-for="card in filteredBoundCards" :key="card.tag_id">
                        <div class="p-4 rounded-2xl bg-slate-50/70 border-2 border-slate-200 hover:border-[#8b1818]/40 hover:bg-white transition space-y-2.5 group shadow-2xs">
                            <div class="flex items-center justify-between gap-2">
                                <!-- UID Pill with Copy Action -->
                                <button type="button" 
                                        @click="copyUid(card.tag_id)" 
                                        class="px-2.5 py-1 bg-white group-hover:bg-[#8b1818]/10 text-slate-900 group-hover:text-[#8b1818] rounded-lg text-[10px] font-mono font-black border border-slate-200 group-hover:border-[#8b1818]/30 transition cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                        title="Click to copy UID">
                                    <i class="fa-solid fa-microchip text-[9px] text-[#8b1818]"></i>
                                    <span x-text="card.tag_id"></span>
                                    <i class="fa-regular fa-copy text-[9px] text-slate-400"></i>
                                </button>

                                <span class="text-[9px] font-bold text-slate-400 flex items-center gap-1">
                                    <i class="fa-regular fa-calendar text-[9px]"></i>
                                    <span x-text="card.date"></span>
                                </span>
                            </div>

                            <!-- Student Details -->
                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                                <div class="flex items-center gap-2.5 overflow-hidden">
                                    <div class="w-7 h-7 rounded-lg bg-[#8b1818]/10 text-[#8b1818] flex items-center justify-center font-black text-[10px] shrink-0 uppercase"
                                         x-text="card.student_name.charAt(0)"></div>
                                    <h4 class="text-xs font-black text-slate-900 truncate" x-text="card.student_name"></h4>
                                </div>
                                <span class="text-[10px] font-mono font-bold bg-white px-2 py-0.5 rounded-md border border-slate-200 text-slate-600 shrink-0" x-text="card.lrn"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State for Search Filter or No Records -->
                    <template x-if="filteredBoundCards.length === 0">
                        <div class="py-12 text-center text-slate-400 font-bold space-y-1 my-auto">
                            <div class="text-xl text-slate-300"><i class="fa-solid fa-id-card-clip"></i></div>
                            <p class="text-xs">No active credential records found.</p>
                        </div>
                    </template>
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