<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $schedule->subject_name }} - Class Roster - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sia-card { background: #ffffff; border: 2px solid #e2e8f0; border-radius: 24px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03); }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex">

    <!-- REUSABLE SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 flex flex-col min-w-0 ml-72">
        
        <!-- Top Navigation Header -->
        <header class="bg-white/85 backdrop-blur-md border-b-2 border-slate-200/80 px-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('teacher.schedules') }}" class="text-xs font-bold text-slate-400 hover:text-[#8b1818] transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Schedule Matrix
                    </a>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $schedule->subject_name }}</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Section: <span class="text-[#8b1818]">{{ $schedule->section }}</span> &bull; Room / Time: {{ $schedule->room ?? 'TBA' }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 text-xs font-black text-[#8b1818] bg-red-50 border border-red-200 px-4 py-2 rounded-xl shadow-2xs">
                    <i class="fa-solid fa-users text-sm"></i> Total Enrolled: {{ $students->count() }} Students
                </span>
            </div>
        </header>

        <!-- Page Body Content -->
        <div class="p-8 max-w-7xl w-full mx-auto space-y-8 flex-1">
            
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Class Roster Table Card -->
            <div class="sia-card p-6 md:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-200 text-[#8b1818] flex items-center justify-center text-base shadow-inner">
                            <i class="fa-solid fa-address-book"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900 tracking-tight">Enrolled Student Roster</h2>
                            <p class="text-xs text-slate-400 font-bold mt-0.5">Official class list for {{ $schedule->subject_name }}</p>
                        </div>
                    </div>
                </div>

                @if($students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-400 uppercase font-black tracking-wider">
                                    <th class="pb-3">#</th>
                                    <th class="pb-3">Student Name</th>
                                    <th class="pb-3">School ID / LRN</th>
                                    <th class="pb-3">Strand / Track</th>
                                    <th class="pb-3">Email Address</th>
                                    <th class="pb-3">Contact Number</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                @foreach($students as $index => $student)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 font-mono text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-4 font-bold text-slate-900 flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300 text-amber-900 overflow-hidden flex items-center justify-center shrink-0 font-black text-xs shadow-2xs">
                                                @if(!empty($student->photo))
                                                    <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? 'P', 0, 1)) }}
                                                @endif
                                            </div>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                        </td>
                                        <td class="py-4 font-mono font-semibold text-slate-600">{{ $student->id_number ?? 'N/A' }}</td>
                                        <td class="py-4">
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $student->strand ?? 'General' }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-slate-500">{{ $student->email }}</td>
                                        <td class="py-4 font-mono text-slate-500">{{ $student->phone_number ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-20 text-center">
                        <div class="w-20 h-20 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200 text-slate-300 flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                            <i class="fa-solid fa-users-slash"></i>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-800">No Students Found</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-1 max-w-sm mx-auto">There are currently no students registered under section "{{ $schedule->section }}".</p>
                    </div>
                @endif
            </div>

        </div>
    </main>
</body>
</html>