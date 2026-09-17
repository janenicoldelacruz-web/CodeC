@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-black text-slate-900">Evaluate Faculty: Prof. {{ $teacher->first_name }} {{ $teacher->last_name }}</h1>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Please rate each criteria objectively from 1 (Lowest) to 5 (Highest).</p>
        </div>
        <a href="{{ route('student.evaluations.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black rounded-2xl transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    <form action="{{ route('student.evaluations.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="evaluatee_id" value="{{ $teacher->id }}">

        @foreach($groupedQuestions as $category => $questions)
            <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 shadow-xs">
                <h3 class="text-xs font-black text-[#8b1818] uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">{{ $category }}</h3>
                <div class="space-y-6">
                    @foreach($questions as $q)
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-slate-800">{{ $q->order_num }}. {{ $q->question }}</p>
                            <div class="flex items-center gap-4">
                                @for($score = 1; $score <= 5; $score++)
                                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-bold text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 transition flex-1 justify-center">
                                        <input type="radio" name="scores[{{ $q->id }}]" value="{{ $score }}" required class="accent-[#8b1818]">
                                        <span>{{ $score }}</span>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 shadow-xs">
            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Additional Comments / Feedback (Optional)</label>
            <textarea name="comments" rows="4" placeholder="Write any constructive feedback or comments for this instructor..." class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8b1818]"></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <button type="submit" class="px-6 py-3 bg-[#8b1818] hover:bg-opacity-90 text-white text-xs font-black rounded-2xl shadow-md transition">
                <i class="fa-solid fa-paper-plane mr-1.5"></i> Submit Evaluation
            </button>
        </div>
    </form>
</div>
@endsection