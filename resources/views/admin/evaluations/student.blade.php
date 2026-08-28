<div id="tabStudentContent" class="space-y-6">
    <div class="p-6 lg:p-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight">Student Subject Teacher Evaluations</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Fetched directly from database with Grade Level & Section filtering</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 uppercase font-black tracking-wider text-xs border-b border-slate-200">
                        <th class="py-3.5 px-4">Student Evaluator</th>
                        <th class="py-3.5 px-4">Grade & Section</th>
                        <th class="py-3.5 px-4">Subject Teacher</th>
                        <th class="py-3.5 px-4 text-center">Overall Score</th>
                        <th class="py-3.5 px-4 text-center">Actions</th>
                        <th class="py-3.5 px-4">Written Feedback</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                    @forelse($studentEvaluations as $eval)
                        @php
                            $score = (float)($eval->overall_rating ?? ($eval->rating ?? ($eval->score ?? 5.0)));
                        @endphp
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="py-3.5 px-4">
                                <span class="student-name-text text-slate-900 font-extrabold block leading-tight" data-real-name="{{ $eval->student_first_name ?? 'Student' }} {{ $eval->student_last_name ?? '' }}">
                                    {{ $eval->student_first_name ?? 'Student' }} {{ $eval->student_last_name ?? '' }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">Enrolled Student</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="font-extrabold text-slate-800 block">Grade {{ $eval->student_grade_level ?? 'N/A' }}</span>
                                <span class="text-slate-500 font-semibold">{{ $eval->student_track ?? 'General' }} • Section: {{ $eval->student_section ?? 'N/A' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-900 font-extrabold block leading-tight">
                                    {{ $eval->teacher_first_name ?? 'Faculty' }} {{ $eval->teacher_last_name ?? '' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">{{ $eval->teacher_strand ?? 'Subject Instructor' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-mono text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                                    ★ {{ number_format($score, 1) }} / 5.0
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                    onclick="openFullReportModal('{{ $eval->teacher_first_name ?? 'Teacher' }} {{ $eval->teacher_last_name ?? '' }}', '{{ $eval->student_section ?? 'N/A' }}', {{ $score }}, '{{ $eval->comments ?? $eval->remarks ?? 'No remarks' }}')" 
                                    class="px-3.5 py-1.5 rounded-full bg-red-50 hover:bg-red-100 border border-red-200 text-[#8b1818] text-xs font-black transition cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
                                    <span>View Full Report</span>
                                </button>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs max-w-xs">
                                <div class="truncate font-medium bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700">
                                    "{{ $eval->comments ?? ($eval->remarks ?? 'Constructive feedback submitted.') }}"
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                <p class="text-sm font-extrabold text-slate-800">No Student Evaluations Found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($studentEvaluations, 'hasPages') && $studentEvaluations->hasPages())
            <div class="pt-2">
                {{ $studentEvaluations->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>