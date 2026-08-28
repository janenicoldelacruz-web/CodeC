<div id="tabPeerContent" class="space-y-6 hidden">
    <div class="p-6 lg:p-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight">Faculty Peer Evaluations</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Colleague assessments, department collaboration rubrics, and professional ethics</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 uppercase font-black tracking-wider text-xs border-b border-slate-200">
                        <th class="py-3.5 px-4">Evaluating Peer (Colleague)</th>
                        <th class="py-3.5 px-4">Faculty Evaluated</th>
                        <th class="py-3.5 px-4 text-center">Peer Score</th>
                        <th class="py-3.5 px-4">Collaboration & Ethics Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                    @forelse($peerEvaluations as $eval)
                        @php
                            $score = (float)($eval->overall_rating ?? ($eval->rating ?? ($eval->score ?? 5.0)));
                        @endphp
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-3.5 px-4">
                                <span class="student-name-text text-slate-900 font-extrabold block leading-tight" data-real-name="{{ $eval->peer_first_name ?? 'Colleague' }} {{ $eval->peer_last_name ?? '' }}">
                                    {{ $eval->peer_first_name ?? 'Colleague' }} {{ $eval->peer_last_name ?? '' }}
                                </span>
                                <span class="text-[10px] text-amber-700 font-bold uppercase">Evaluating Faculty Peer</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-900 font-extrabold block leading-tight">
                                    {{ $eval->teacher_first_name ?? 'Faculty' }} {{ $eval->teacher_last_name ?? '' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">Subject Teacher</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-mono text-xs font-black bg-amber-50 text-amber-800 border border-amber-200 shadow-2xs">
                                    ★ {{ number_format($score, 1) }} / 5.0
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs max-w-xs">
                                <div class="truncate font-medium bg-slate-50 p-2 rounded-xl border border-slate-200 text-slate-700">
                                    "{{ $eval->comments ?? ($eval->remarks ?? 'Peer collaboration review submitted.') }}"
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 font-medium">
                                <p class="text-sm font-extrabold text-slate-800">No Peer Evaluations Found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>