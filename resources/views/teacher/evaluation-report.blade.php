<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Report View - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .folder-card { position: relative; background: #ffffff; border: 2px solid #b91c1c; border-radius: 18px; }
        .triple-pills { display: inline-flex; gap: 4px; }
        .triple-pills span { width: 10px; height: 24px; background-color: #b91c1c; border-radius: 9999px; transform: skewX(-15deg); }
    </style>
</head>
<body class="bg-[#fcfbfb] text-gray-800 antialiased min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r-2 border-red-100 flex flex-col justify-between shrink-0 h-screen sticky top-0">
        <div>
            <div class="p-6 pb-5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tight text-[#b91c1c]">SIATRACK</span>
                    <i class="fa-solid fa-wifi rotate-45 text-[#b91c1c] text-lg"></i>
                </div>
                <p class="text-[11px] font-bold text-gray-500 mt-0.5">Southern Isabela Academy</p>
            </div>

            <nav class="p-4 space-y-2 text-sm font-bold">
                <a href="{{ route('teacher.schedules') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-users text-sm"></i>
                    <span>Class Attendance View</span>
                </a>
                <a href="{{ route('teacher.attendance') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-clipboard-user text-sm"></i>
                    <span>Absence Reporting</span>
                </a>
                <a href="{{ route('teacher.evaluation.report') }}" class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm">
                    <i class="fa-solid fa-chalkboard-user text-sm"></i>
                    <span>Evaluation Report View</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 text-xs font-bold text-gray-600 hover:text-red-600 transition">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Evaluation Report View</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm font-black text-gray-900">Faculty: {{ $teacher->first_name }} {{ $teacher->last_name }}</span>
                <button class="relative text-gray-700 hover:text-red-600 text-lg"><i class="fa-regular fa-bell"></i></button>
            </div>
        </header>

        <div class="p-8 max-w-7xl w-full mx-auto space-y-8">
            
            <!-- Top Section: Overview Cards + Performance Metrics (Real Data) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: 1. Faculty Evaluation Overview -->
                <div class="lg:col-span-8 space-y-3">
                    <h2 class="text-base font-black text-gray-900">1. Faculty Evaluation Overview</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Average Rating Score -->
                        <div class="folder-card p-6 flex flex-col justify-between min-h-[140px]">
                            <span class="text-xs font-bold text-gray-700">Average Rating Score</span>
                            <span class="text-4xl font-black text-gray-900">
                                {{ $averageRating > 0 ? $averageRating . '/5' : 'N/A' }}
                            </span>
                            <div class="flex justify-end"><div class="triple-pills"><span></span><span></span><span></span></div></div>
                        </div>

                        <!-- Total Evaluation Count -->
                        <div class="folder-card p-6 flex flex-col justify-between min-h-[140px]">
                            <span class="text-xs font-bold text-gray-700">Total Evaluation Submissions</span>
                            <span class="text-4xl font-black text-gray-900">{{ number_format($totalSubmissions) }}</span>
                            <div class="flex justify-end"><div class="triple-pills"><span></span><span></span><span></span></div></div>
                        </div>
                    </div>
                </div>

                <!-- Right: 2. Performance Metrics -->
                <div class="lg:col-span-4 space-y-3">
                    <h2 class="text-base font-black text-gray-900">2. Performance Metrics</h2>
                    
                    <div class="folder-card p-4 space-y-2">
                        <span class="text-xs font-black text-gray-900 block">Quick Overview</span>
                        <div class="border border-gray-300 rounded-xl overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs font-semibold">
                                <thead>
                                    <tr class="bg-[#b91c1c] text-white text-[10px] font-black uppercase">
                                        <th class="py-2 px-3">Criteria</th>
                                        <th class="py-2 px-3 text-center">Overall Score</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($metrics as $m)
                                        <tr class="bg-gray-50">
                                            <td class="py-2 px-3 text-gray-800">{{ $m['criteria'] }}</td>
                                            <td class="py-2 px-3 text-center font-bold text-gray-900">{{ $m['score'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-end pt-1"><div class="triple-pills"><span></span><span></span><span></span></div></div>
                    </div>
                </div>

            </div>

            <!-- Bottom Section: 3. General Student Feedback Summary (Real Data Only) -->
            <div>
                <h2 class="text-base font-black text-gray-900 mb-3">3. General Student Feedback Summary (Anonymized)</h2>
                
                <div class="folder-card p-6 space-y-4">
                    <div class="border border-gray-300 rounded-xl overflow-hidden">
                        <table class="w-full text-left border-collapse text-xs font-semibold">
                            <thead>
                                <tr class="bg-[#b91c1c] text-white text-[11px] font-black uppercase">
                                    <th class="py-3 px-6 w-1/3">Overall Tone</th>
                                    <th class="py-3 px-6">Student Feedback</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($feedbackList as $idx => $f)
                                    <tr class="{{ $idx % 2 === 0 ? 'bg-gray-100' : 'bg-gray-50' }}">
                                        <td class="py-3 px-6 font-black text-gray-900">
                                            @php
                                                $rating = $f->overall_rating ?? ($f->rating ?? 5.0);
                                            @endphp
                                            @if($rating >= 4.0)
                                                <span class="text-green-700">Highly Positive</span>
                                            @elseif($rating >= 3.0)
                                                <span class="text-yellow-700">Neutral / Satisfactory</span>
                                            @else
                                                <span class="text-red-700">Needs Improvement</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-gray-700 font-medium">"{{ $f->comments ?? ($f->remarks ?? 'Student feedback recorded.') }}"</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-8 text-center text-gray-400 font-bold">
                                            <i class="fa-solid fa-comment-dots text-2xl text-gray-300 mb-2 block"></i>
                                            No student evaluations submitted for your profile yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <div class="triple-pills"><span></span><span></span><span></span></div>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>