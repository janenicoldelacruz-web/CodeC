@extends('layouts.app')

@section('title', 'Evaluation Periods & Questions - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70 font-sans pb-12">

    <!-- Header Navigation -->
    <header class="bg-white border-b-2 border-slate-200 px-6 lg:px-10 py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.evaluations') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center justify-center text-slate-600 transition shadow-2xs">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight">Evaluation Periods & Rubrics</h1>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300 uppercase">DEPED 3-TERM</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Manage institutional academic evaluation cycles, schedule windows, and questionnaire rubrics</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- MASTER ON/OFF SWITCH FOR STUDENT PORTAL -->
            <div class="flex items-center gap-3 bg-slate-50 p-2 px-4 rounded-xl border-2 border-slate-200 shadow-2xs">
                <span class="text-[11px] font-black text-slate-600 uppercase tracking-wider">Student Portal:</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="evalToggleMaster" {{ \Illuminate\Support\Facades\Cache::get('evaluations_open', false) ? 'checked' : '' }} onchange="toggleMasterEvaluation(this)" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8b1818]"></div>
                </label>
                <span id="evalStatusText" class="{{ \Illuminate\Support\Facades\Cache::get('evaluations_open', false) ? 'text-emerald-600' : 'text-slate-400' }} text-xs font-black uppercase tracking-widest w-16 text-center">
                    {{ \Illuminate\Support\Facades\Cache::get('evaluations_open', false) ? 'OPEN' : 'CLOSED' }}
                </span>
            </div>

            <button type="button" class="px-4 py-2.5 bg-[#8b1818] hover:bg-[#701313] text-white text-xs font-black rounded-xl shadow-xs flex items-center gap-2 transition cursor-pointer">
                <i class="fa-solid fa-calendar-plus text-amber-300"></i>
                <span class="hidden sm:inline">New Period</span>
            </button>
        </div>
    </header>

    <main class="p-6 lg:p-10 max-w-[1600px] w-full mx-auto space-y-8 flex-1">

        <!-- ================= SECTION 1: ACADEMIC CYCLES & PERIODS ================= -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-[#8b1818]"></i>
                    <span>Academic Term Evaluation Schedules</span>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($periods as $p)
                <div class="p-5 rounded-2xl bg-white border-2 {{ $p->is_active ? 'border-emerald-300 ring-2 ring-emerald-500/10' : 'border-slate-200' }} shadow-xs flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">A.Y. {{ $p->school_year ?? '2027-2028' }}</span>
                            <h3 class="text-lg font-black text-slate-900 mt-0.5">{{ $p->term ?? 'Term ' . $loop->iteration }}</h3>
                            <span class="text-xs font-bold text-slate-500">{{ $p->semester ?? 'Academic Cycle' }}</span>
                        </div>
                        @if($p->is_active)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span> ACTIVE
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200">INACTIVE</span>
                        @endif
                    </div>

                    <div class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1 text-xs">
                        <div class="flex justify-between text-slate-600 font-bold">
                            <span>Start Date:</span>
                            <span class="font-mono text-slate-800">{{ $p->start_date ?? 'Aug 15, 2027' }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-bold">
                            <span>End Date:</span>
                            <span class="font-mono text-slate-800">{{ $p->end_date ?? 'Oct 30, 2027' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Status</span>
                        @if($p->is_active)
                            <span class="text-xs font-black text-emerald-700">Open for Submissions</span>
                        @else
                            <span class="text-xs font-black text-slate-500">Submissions Closed</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ================= SECTION 2: EVALUATION CRITERIA & QUESTIONS ================= -->
        <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 lg:p-8 space-y-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b-2 border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center text-base font-black shadow-2xs">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">Standard Evaluation Rubrics & Questions</h2>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5">View and manage categories and assessment items in bulk.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="openCategoryModal('new', '', '')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-black shadow-2xs transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-layer-group"></i> Add New Category
                    </button>
                </div>
            </div>

            <!-- Questionnaire Dimensions Accordion List (Clean Read-Only View) -->
            <div class="space-y-6">
                @foreach($criteria as $key => $dim)
                <div class="rounded-2xl border-2 border-slate-200 bg-white overflow-hidden shadow-2xs">
                    <!-- Category Header -->
                    <div class="p-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#8b1818] text-white text-xs font-black flex items-center justify-center">{{ $loop->iteration }}</span>
                            <h4 class="text-sm font-black text-slate-900">{{ $dim['title'] }}</h4>
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-100 text-amber-900 text-[10px] font-black border border-amber-200 ml-1">Weight: {{ $dim['weight'] }}</span>
                        </div>
                        
                        <!-- Isang Edit Button lang per Container -->
                        <button onclick="openCategoryModal('{{ $key }}', '{{ htmlspecialchars($dim['title'], ENT_QUOTES) }}', '{{ $dim['weight'] }}', {{ json_encode($dim['questions']) }})" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-2 transition cursor-pointer shadow-sm" title="Edit Category & Questions">
                            <i class="fa-solid fa-pen-to-square"></i> Edit Category
                        </button>
                    </div>
                    
                    <!-- Clean Questions List -->
                    <div class="p-4 divide-y divide-slate-100">
                        @foreach($dim['questions'] as $q)
                        <div class="py-2.5 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs mt-1 shrink-0"></i>
                            <span class="text-xs font-bold text-slate-700 leading-relaxed">{{ $q }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </main>
</div>

<!-- ================= BULK EDIT MODAL ================= -->
<div id="categoryModal" class="fixed inset-0 z-50 bg-slate-900/60 hidden items-center justify-center p-4 sm:p-6" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">
        
        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/60 shrink-0">
            <div>
                <h3 id="categoryModalTitle" class="text-lg font-black text-slate-800 tracking-tight">Edit Category & Questions</h3>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Modify the title, weight, and all evaluation questions for this section.</p>
            </div>
            <button onclick="closeCategoryModal()" class="w-8 h-8 rounded-full bg-slate-200/60 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form Body (Scrollable) -->
        <div class="p-6 overflow-y-auto flex-1 space-y-6 bg-white">
            <input type="hidden" id="categoryKey">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category Title</label>
                    <input type="text" id="categoryTitleInput" placeholder="e.g. Subject Mastery" class="w-full px-4 py-2.5 text-sm font-semibold border-2 border-slate-200 rounded-xl focus:border-[#8b1818] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Weight</label>
                    <input type="text" id="categoryWeightInput" placeholder="e.g. 25%" class="w-full px-4 py-2.5 text-sm font-semibold border-2 border-slate-200 rounded-xl focus:border-[#8b1818] outline-none">
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between mb-4 mt-2">
                    <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">Evaluation Questions</label>
                    <button onclick="addQuestionField()" class="text-[11px] font-black text-[#8b1818] hover:text-red-700 flex items-center gap-1.5 transition cursor-pointer bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg border border-red-200">
                        <i class="fa-solid fa-plus"></i> Add New Question
                    </button>
                </div>
                
                <!-- Dynamic Input Container -->
                <div id="modalQuestionsContainer" class="space-y-3">
                    <!-- JS will inject questions here -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/60 flex justify-end gap-3 shrink-0">
            <button onclick="closeCategoryModal()" class="px-5 py-2.5 rounded-xl border-2 border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-extrabold transition cursor-pointer">Cancel</button>
            <button onclick="saveCategory()" class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#721313] text-white text-xs font-black shadow-md transition flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i> Save All Changes
            </button>
        </div>

    </div>
</div>

<!-- CSRF Token for AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Toggle System
    function toggleMasterEvaluation(checkbox) {
        const statusText = document.getElementById('evalStatusText');
        const isOpen = checkbox.checked;

        fetch('/admin/evaluations/toggle-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: isOpen })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network issue');
            return response.json();
        })
        .then(data => {
            if (data.is_open) {
                statusText.innerText = 'OPEN';
                statusText.className = 'text-emerald-600 text-xs font-black uppercase tracking-widest w-16 text-center';
                if (typeof Swal !== 'undefined') Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }).fire({ icon: 'success', title: 'Evaluation is now OPEN for students!' });
            } else {
                statusText.innerText = 'CLOSED';
                statusText.className = 'text-slate-400 text-xs font-black uppercase tracking-widest w-16 text-center';
                if (typeof Swal !== 'undefined') Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 }).fire({ icon: 'info', title: 'Evaluation portal CLOSED.' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !isOpen; 
            if (typeof Swal !== 'undefined') Swal.fire('Error', 'Failed to connect to the server.', 'error');
        });
    }

    // Modal System
    function openCategoryModal(key, title, weight, questions = []) {
        document.getElementById('categoryModalTitle').innerText = key === 'new' ? 'Add New Category & Questions' : 'Edit Category & Questions';
        document.getElementById('categoryKey').value = key !== 'new' ? key : '';
        document.getElementById('categoryTitleInput').value = title;
        document.getElementById('categoryWeightInput').value = weight;
        
        const container = document.getElementById('modalQuestionsContainer');
        container.innerHTML = ''; // Clear old entries
        
        if (questions && questions.length > 0) {
            questions.forEach(q => addQuestionField(q));
        } else {
            addQuestionField(); // Add at least one empty box if new
        }

        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('categoryModal').classList.add('flex');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('flex');
    }

    // Dynamic Form Builder
    function addQuestionField(text = '') {
        const container = document.getElementById('modalQuestionsContainer');
        const div = document.createElement('div');
        div.className = "flex items-start gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200 shadow-2xs group";
        
        div.innerHTML = `
            <div class="mt-2 text-slate-300 shrink-0 pl-1"><i class="fa-solid fa-grip-vertical"></i></div>
            <textarea rows="2" class="w-full px-3 py-2 text-xs font-semibold border-2 border-slate-200 rounded-lg focus:border-[#8b1818] outline-none resize-none bg-white" placeholder="Enter evaluation statement/question here..."></textarea>
            <button type="button" onclick="this.parentElement.remove()" class="mt-1 w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center shrink-0 transition" title="Remove Question">
                <i class="fa-solid fa-trash text-[10px]"></i>
            </button>
        `;
        
        div.querySelector('textarea').value = text;
        container.appendChild(div);
    }

    function saveCategory() {
        // Collect Data (Simulation for backend logic)
        const title = document.getElementById('categoryTitleInput').value;
        const weight = document.getElementById('categoryWeightInput').value;
        
        const questionInputs = document.querySelectorAll('#modalQuestionsContainer textarea');
        let questions = [];
        questionInputs.forEach(input => {
            if(input.value.trim() !== '') questions.push(input.value.trim());
        });

        if (!title || questions.length === 0) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Title and at least one question are required!', confirmButtonColor: '#8b1818' });
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Saved Successfully!',
            text: `Category updated with ${questions.length} questions.`,
            confirmButtonColor: '#8b1818'
        }).then(() => {
            closeCategoryModal();
            // location.reload(); // I-uncomment kapag may DB save na sa backend
        });
    }
</script>
@endsection