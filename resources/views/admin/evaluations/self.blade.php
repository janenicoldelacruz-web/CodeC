<div id="tabSelfContent" class="space-y-6 hidden">
    <div class="p-6 lg:p-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight">Faculty Self-Evaluations</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Pedagogical growth targets, instructional goal achievements, and self-reflection</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 uppercase font-black tracking-wider text-xs border-b border-slate-200">
                        <th class="py-3.5 px-4">Faculty Member</th>
                        <th class="py-3.5 px-4">Strand / Specialization</th>
                        <th class="py-3.5 px-4 text-center">Self-Rating</th>
                        <th class="py-3.5 px-4">Reflective Growth Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                    @forelse($selfEvaluations as $eval)
                        @php
                            $score = (float)($eval->overall_rating ?? ($eval->rating ?? ($eval->score ?? 5.0)));
                        @endphp
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="py-3.5 px-4">
                                <span class="text-slate-900 font-extrabold block leading-tight">
                                    {{ $eval->teacher_first_name ?? 'Faculty' }} {{ $eval->teacher_last_name ?? '' }}
                                </span>
                                <span class="text-[10px] text-blue-700 font-bold uppercase">Self Assessment</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="font-extrabold text-slate-800">{{ $eval->teacher_strand ?? 'General Academics' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-mono text-xs font-black bg-blue-50 text-blue-800 border border-blue-200 shadow-2xs">
                                    ★ {{ number_format($score, 1) }} / 5.0
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs max-w-xs">
                                <div class="truncate font-medium bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700">
                                    "{{ $eval->comments ?? ($eval->remarks ?? 'Self-reflection submitted.') }}"
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 font-medium">
                                <p class="text-sm font-extrabold text-slate-800">No Self Evaluations Found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>