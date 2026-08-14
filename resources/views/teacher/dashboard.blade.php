<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Attendance View - SIATRACK</title>
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
            <!-- Branding Header -->
            <div class="p-6 pb-5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black tracking-tight text-[#b91c1c]">SIATRACK</span>
                    <i class="fa-solid fa-wifi rotate-45 text-[#b91c1c] text-lg"></i>
                </div>
                <p class="text-[11px] font-semibold text-gray-400 mt-0.5">
                    Southern Isabela Academy
                </p>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-2 text-sm font-semibold">
                <!-- Class Attendance View (Active) -->
                <a href="{{ route('teacher.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm transition">
                    <i class="fa-solid fa-users text-sm"></i>
                    <span>Class Attendance View</span>
                </a>

                <!-- Absence Reporting -->
                <a href="{{ route('teacher.absence.reporting') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-file-invoice text-sm text-teal-600"></i>
                    <span>Absence Reporting</span>
                </a>

                <!-- Evaluation Report View -->
                <a href="{{ route('teacher.evaluation.report') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-chart-simple text-sm text-gray-500"></i>
                    <span>Evaluation Report View</span>
                </a>
            </nav>
        </div>

        <!-- Faculty Profile & Logout -->
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
        <!-- Top Header Bar -->
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">
                Class Attendance View
            </h1>

            <div class="flex items-center gap-6">
                <!-- Search Bar -->
                <form method="GET" action="{{ route('teacher.dashboard') }}" class="relative w-72">
                    @if(!empty($selectedStrand))
                        <input type="hidden" name="strand" value="{{ $selectedStrand }}">
                    @endif
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-red-600">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search Student Name or ID..." 
                           class="w-full pl-9 pr-4 py-1.5 text-xs bg-white border border-red-500 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-600 placeholder-gray-400">
                </form>

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

        <!-- Body Area -->
        <div class="p-8 space-y-8 max-w-7xl w-full">
            <!-- SECTION 1: Session Info & Strand Cards -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-base font-extrabold text-gray-900">
                        1. Session Information header
                    </h2>
                    <div class="flex items-center border border-gray-400 rounded-full px-2.5 py-0.5 text-[11px] font-semibold gap-1.5 bg-white">
                        <span class="text-gray-700 font-medium">Live Connectivity Status</span>
                        <span id="conn-badge" class="bg-[#16a34a] text-white px-2 py-0.5 rounded-full text-[9px] uppercase font-bold tracking-wider">
                            Active
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    <!-- Active Class Cards -->
                    <div class="lg:col-span-8 space-y-4">
                        @forelse($activeClasses as $class)
                            <div class="bg-white border-2 border-red-300 rounded-3xl p-5 shadow-xs relative">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-sm font-black text-gray-900">
                                            Active Class: {{ $class->title }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1 font-semibold">
                                            {{ $class->subject }} &bull; <span class="text-red-600 font-bold">{{ $class->room }}</span>
                                        </p>
                                        <p class="text-xs font-bold text-gray-900 mt-1">
                                            Scheduled Time: <span class="font-normal text-gray-600">{{ $class->time }}</span>
                                        </p>
                                    </div>
                                    <button class="text-gray-600 hover:text-red-600 p-1" title="Edit Class Details">
                                        <i class="fa-regular fa-pen-to-square text-base"></i>
                                    </button>
                                </div>
                                <div class="flex justify-end gap-1.5 mt-2">
                                    <span class="w-5 h-2.5 bg-[#881337] rounded-sm slanted-pill"></span>
                                    <span class="w-5 h-2.5 bg-[#881337] rounded-sm slanted-pill"></span>
                                    <span class="w-5 h-2.5 bg-[#881337] rounded-sm slanted-pill"></span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 bg-white border border-gray-200 rounded-2xl text-center text-sm text-gray-500">
                                No active classes scheduled right now.
                            </div>
                        @endforelse
                    </div>

                    <!-- Strand Filter Cards -->
                    <div class="lg:col-span-4">
                        <div class="bg-white border-2 border-red-300 rounded-3xl p-6 h-full flex flex-col justify-center relative shadow-xs">
                            <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>

                            <div class="grid grid-cols-2 gap-4">
                                @php $strands = ['ABM', 'GAS', 'HUMSS', 'STEM']; @endphp
                                @foreach($strands as $st)
                                    @php
                                        $isSelected = ($selectedStrand === $st);
                                        $url = $isSelected 
                                            ? route('teacher.dashboard', array_filter(['search' => $search])) 
                                            : route('teacher.dashboard', array_filter(['strand' => $st, 'search' => $search]));
                                    @endphp
                                    <a href="{{ $url }}" 
                                       class="h-20 {{ $isSelected ? 'bg-gradient-to-b from-[#111827] to-[#1f2937] ring-4 ring-red-500 scale-105' : 'bg-gradient-to-b from-[#dc2626] to-[#991b1b] hover:brightness-110' }} text-white font-extrabold text-sm rounded-2xl shadow-md flex flex-col items-center justify-center tracking-wider transition">
                                        <span>{{ $st }}</span>
                                        @if($isSelected)
                                            <span class="text-[9px] font-bold text-red-400 mt-1 uppercase">Selected</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            @if(!empty($selectedStrand))
                                <div class="mt-4 text-center">
                                    <a href="{{ route('teacher.dashboard', array_filter(['search' => $search])) }}" class="text-xs font-bold text-red-600 hover:underline">
                                        &times; Clear Strand Filter
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 3: Real-Time Attendance List (Live Log) -->
            <section class="space-y-4">
                <div class="bg-white border-2 border-red-300 rounded-3xl p-6 shadow-xs relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <h2 class="text-base font-extrabold text-gray-900">
                                3. Real-Time Attendance List (Live Log)
                            </h2>
                            @if(!empty($selectedStrand))
                                <span class="bg-red-100 text-red-700 text-xs px-2.5 py-0.5 rounded-full font-bold">
                                    Filtered by: {{ $selectedStrand }}
                                </span>
                            @endif
                        </div>
                        <div class="border border-red-300 rounded-lg p-1 text-center w-8 h-8 flex flex-col items-center justify-center bg-gray-50 shadow-2xs">
                            <span class="text-[10px] font-black text-red-600 leading-none">{{ date('d') }}</span>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#b91c1c] text-white text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-6 text-center w-28">Student Photo</th>
                                    <th class="py-3 px-6 border-l border-red-400">Name & ID Number</th>
                                    <th class="py-3 px-6 border-l border-red-400 text-center w-48">Time-In Stamp</th>
                                    <th class="py-3 px-6 border-l border-red-400 text-center w-48">Status Label</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300 text-xs font-semibold text-gray-900 bg-[#e5e7eb]">
                                @forelse($attendanceLogs as $log)
                                    <tr class="hover:bg-gray-300 transition">
                                        <td class="py-2.5 px-6 text-center">
                                            <div class="w-10 h-10 mx-auto rounded-full border-2 border-gray-900 flex items-center justify-center text-gray-800 bg-white">
                                                <i class="fa-regular fa-user text-base"></i>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-6 border-l border-gray-400">
                                            <p class="font-bold text-gray-900 text-sm">{{ $log->name }}</p>
                                            <p class="text-gray-600 font-medium">ID: {{ $log->id_number }} @if($log->strand !== 'N/A') &bull; <span class="text-xs text-red-800 font-bold">{{ $log->strand }}</span> @endif</p>
                                        </td>
                                        <td class="py-2.5 px-6 border-l border-gray-400 text-center text-gray-800 font-bold">
                                            {{ $log->time_in }}
                                        </td>
                                        <td class="py-2.5 px-6 border-l border-gray-400 text-center">
                                            @if($log->status === 'ON-TIME' || $log->status === 'PRESENT')
                                                <span class="inline-block bg-[#22c55e] text-white px-7 py-1 rounded-full text-[11px] font-black uppercase tracking-wider">
                                                    ON-TIME
                                                </span>
                                            @elseif($log->status === 'LATE')
                                                <span class="inline-block bg-[#fef08a] text-[#854d0e] px-7 py-1 rounded-full text-[11px] font-black uppercase tracking-wider">
                                                    Late
                                                </span>
                                            @else
                                                <span class="inline-block bg-[#991b1b] text-white px-7 py-1 rounded-full text-[11px] font-black uppercase tracking-wider">
                                                    Absent
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-500 bg-white">
                                            <p class="font-bold text-sm">No attendance records found.</p>
                                        </td>
                                    </tr>
                                @endforelse
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

    <script>
        function updateOnlineStatus() {
            const badge = document.getElementById('conn-badge');
            if (navigator.onLine) {
                badge.innerText = 'Active';
                badge.className = 'bg-[#16a34a] text-white px-2 py-0.5 rounded-full text-[9px] uppercase font-bold tracking-wider';
            } else {
                badge.innerText = 'Offline';
                badge.className = 'bg-[#dc2626] text-white px-2 py-0.5 rounded-full text-[9px] uppercase font-bold tracking-wider';
            }
        }
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();
    </script>
</body>
</html>