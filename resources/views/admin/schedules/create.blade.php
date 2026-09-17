<div id="scheduleModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 sm:p-6">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden flex flex-col max-h-[92vh]">
        <div class="px-8 py-5 border-b-2 border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-slate-900">Create Class Schedule</h3>
            <button type="button" onclick="closeScheduleModal()" class="w-9 h-9 rounded-xl hover:bg-slate-200 text-slate-500 font-bold cursor-pointer">✕</button>
        </div>
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-8 overflow-y-auto space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Subject Name *</label>
                <input type="text" name="subject_name" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Assigned Faculty *</label>
                <select name="teacher_id" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                    <option value="">Select Faculty</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Grade *</label>
                    <select name="grade_level" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="">Select Grade</option>
                        @foreach($dbGrades as $g) <option value="{{ $g }}">{{ $g }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Strand *</label>
                    <select name="strand" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="">Select Strand</option>
                        @foreach($dbStrands as $s) <option value="{{ $s }}">{{ $s }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Section *</label>
                    <select name="section" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="">Select Section</option>
                        @foreach($dbSections as $sec) <option value="{{ $sec->section_name }}">{{ $sec->section_name }}</option> @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Day *</label>
                    <select name="day" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white cursor-pointer">
                        <option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Start Time *</label>
                    <input type="time" name="start_time" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">End Time *</label>
                    <input type="time" name="end_time" required class="w-full py-2.5 px-3 text-sm font-semibold rounded-xl border border-slate-300 focus:border-[#8b1818] outline-none bg-white">
                </div>
            </div>
            <div class="pt-4 border-t flex justify-end gap-3">
                <button type="button" onclick="closeScheduleModal()" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold cursor-pointer">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#8b1818] text-white text-xs font-black cursor-pointer">Save Schedule</button>
            </div>
        </form>
    </div>
</div>