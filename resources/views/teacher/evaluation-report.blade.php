<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Evaluation Report View - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .slanted-pill { transform: skewX(-20deg); }
    </style>
</head>

<body class="bg-[#fcfbfb] text-gray-800 antialiased min-h-screen flex">

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="w-64 bg-white border-r border-red-100 flex flex-col justify-between shrink-0 h-screen sticky top-0">
        <div>
            <div class="p-6 pb-5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tight text-[#b91c1c]">SIATRACK</span>
                    <i class="fa-solid fa-wifi rotate-45 text-[#b91c1c] text-lg"></i>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">
                    Southern Isabela Academy
                </p>
            </div>

            <nav class="p-4 space-y-2 text-sm font-semibold">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-users text-sm"></i>
                    <span>Class Attendance View</span>
                </a>

                <a href="{{ route('teacher.absence.reporting') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-file-invoice text-sm text-teal-600"></i>
                    <span>Absence Reporting</span>
                </a>

                <!-- Active Item -->
                <a href="{{ route('teacher.evaluation.report') }}" 
                   class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm transition">
                    <i class="fa-solid fa-chart-simple text-sm"></i>
                    <span>Evaluation Report View</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50/60">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs border border-red-200">
                        {{ strtoupper(substr(auth()->user()->first_name ?? 'F', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            {{ auth()->user()->first_name ?? 'Faculty' }} {{ auth()->user()->last_name ?? 'Member' }}
                        </p>
                        <p class="text-[10px] text-red-600 font-bold uppercase tracking-wider">Faculty</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="text-gray-400 hover:text-red-600 p-1.5 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">
                Evaluation Report View
            </h1>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-bold text-gray-900 hover:text-red-600 cursor-pointer">
                        Faculty Profile
                    </span>
                    <button class="relative text-gray-800 hover:text-red-600 transition text-lg">
                        <i class="fa-regular fa-bell"></i>
                    </button>
                </div>
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-7xl w-full">

            <!-- SECTION 1 & 2: Overview & Performance Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left: 1. Faculty Evaluation Overview (7 cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <h2 class="text-base font-extrabold text-gray-900">
                        1. Faculty Evaluation Overview
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Average Rating Score -->
                        <div class="bg-white border-2 border-red-300 rounded-3xl p-6 relative flex flex-col justify-between shadow-xs">
                            <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                            <div class="py-2">
                                <p class="text-sm font-bold text-gray-700">Average Rating Score</p>
                                <p class="text-4xl font-black text-gray-900 mt-2">{{ number_format($overallAverage, 1) }}<span class="text-lg text-gray-500 font-semibold">/5</span></p>
                            </div>
                            <div class="flex justify-end gap-1.5 mt-2">
                                <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                                <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                                <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            </div>
                        </div>

                        <!-- Anonymized Total Count -->
                        <div class="bg-white border-2 border-red-300 rounded-3xl p-6 relative flex flex-col justify-between shadow-xs">
                            <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                            <div class="py-2">
                                <p class="text-sm font-bold text-gray-700">Anonymized Total Evaluation Count</p>
                                <p class="text-4xl font-black text-gray-900 mt-2">{{ $totalEvaluations }}</p>
                            </div>
                            <div class="flex justify-end gap-1.5 mt-2">
                                <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                                <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                                <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: 2. Performance Metrics Breakdown (5 cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <h2 class="text-base font-extrabold text-gray-900">
                        2. Performance Metrics
                    </h2>

                    <div class="bg-white border-2 border-red-300 rounded-3xl p-5 shadow-xs relative">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>

                        <p class="text-xs font-black text-gray-900 mb-3 uppercase tracking-wider">Quick Overview</p>

                        <div class="overflow-x-auto rounded-xl">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-gray-800 text-white font-bold">
                                        <th class="py-2.5 px-4">Criteria</th>
                                        <th class="py-2.5 px-4 text-center">Overall Score</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-[#f3f4f6]">
                                    @foreach($performanceMetrics as $metric)
                                        <tr class="hover:bg-gray-200 transition">
                                            <td class="py-2.5 px-4 font-bold text-gray-800">
                                                {{ $metric->criteria }}
                                            </td>
                                            <td class="py-2.5 px-4 text-center font-black text-gray-900">
                                                {{ number_format($metric->score, 1) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-1.5 mt-3">
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                            <span class="w-4 h-2 bg-[#881337] rounded-sm slanted-pill"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SECTION 3: General Student Feedback Summary (Anonymized) -->
            <section class="space-y-4">
                <h2 class="text-base font-extrabold text-gray-900">
                    3. General Student Feedback Summary (Anonymized)
                </h2>

                <div class="bg-white border-2 border-red-300 rounded-3xl p-6 shadow-xs relative">
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-white text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-6 text-center w-56">Overall Tone</th>
                                    <th class="py-3 px-6 border-l border-gray-700">Student Feedback</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300 text-xs font-semibold text-gray-900 bg-[#e5e7eb]">
                                @foreach($studentFeedbacks as $item)
                                    <tr class="hover:bg-gray-300 transition">
                                        <td class="py-4 px-6 text-center">
                                            <span class="inline-block bg-white border border-gray-400 font-extrabold text-gray-900 px-4 py-1.5 rounded-full text-xs shadow-2xs">
                                                {{ $item->tone }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 border-l border-gray-400 font-medium text-gray-800 leading-relaxed">
                                            "{{ $item->feedback }}"
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <span class="w-8 h-4 bg-[#881337] rounded-sm slanted-pill"></span>
                        <span class="w-8 h-4 bg-[#881337] rounded-sm slanted-pill"></span>
                        <span class="w-8 h-4 bg-[#881337] rounded-sm slanted-pill"></span>
                    </div>
                </div>
            </section>

        </div>
    </main>

</body>
</html>