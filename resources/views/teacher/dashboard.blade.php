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

    @php
        $user = auth()->user();
        $avatarPath = $user->profile_picture ?? session('faculty_avatar_' . $user->id);
    @endphp

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
                <a href="{{ route('teacher.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm transition">
                    <i class="fa-solid fa-users text-sm"></i>
                    <span>Class Attendance View</span>
                </a>

                <a href="{{ route('teacher.absence.reporting') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-file-invoice text-sm text-teal-600"></i>
                    <span>Absence Reporting</span>
                </a>

                <a href="{{ route('teacher.evaluation.report') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-chart-simple text-sm text-gray-500"></i>
                    <span>Evaluation Report View</span>
                </a>
            </nav>
        </div>

        <!-- Faculty Profile Footer Button -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/60">
            <div class="flex items-center justify-between">
                <button type="button" onclick="openProfileModal()" class="flex items-center gap-3 text-left group">
                    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-red-200 group-hover:border-red-500 transition shrink-0 bg-red-100 flex items-center justify-center">
                        @if($avatarPath && file_exists(public_path($avatarPath)))
                            <img src="{{ asset($avatarPath) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <span class="font-black text-sm text-red-600">
                                {{ strtoupper(substr($user->first_name ?? 'F', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-gray-800 truncate group-hover:text-red-600 transition">
                            {{ $user->first_name ?? 'Faculty' }} {{ $user->last_name ?? 'Member' }}
                        </p>
                        <p class="text-[10px] text-red-600 font-bold uppercase tracking-wider">Faculty (Edit)</p>
                    </div>
                </button>

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

                <!-- Faculty Profile Button & Avatar -->
                <div class="flex items-center gap-4">
                    <button type="button" 
                            onclick="openProfileModal()" 
                            class="flex items-center gap-2.5 text-sm font-bold text-gray-900 hover:text-red-600 cursor-pointer transition bg-gray-50 px-3.5 py-1.5 rounded-full border border-gray-200 hover:border-red-300">
                        <div class="w-6 h-6 rounded-full overflow-hidden bg-red-100 flex items-center justify-center text-[11px] font-bold text-red-600 shrink-0">
                            @if($avatarPath && file_exists(public_path($avatarPath)))
                                <img src="{{ asset($avatarPath) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->first_name ?? 'F', 0, 1)) }}
                            @endif
                        </div>
                        <span>Faculty Profile</span>
                    </button>

                    <button type="button" 
                            onclick="alert('No new system notifications.')"
                            class="relative text-gray-800 hover:text-red-600 transition text-lg p-1.5 rounded-full hover:bg-gray-100">
                        <i class="fa-regular fa-bell"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-600 rounded-full"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Body Area -->
        <div class="p-8 space-y-8 max-w-7xl w-full">

            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 font-bold rounded-2xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 font-bold rounded-2xl">
                    <ul class="list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                            <div class="bg-white border-2 border-red-300 rounded-3xl p-5 shadow-xs relative transition hover:shadow-md">
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
                                    
                                    <button type="button" 
                                            onclick="openEditModal('{{ $class->id }}', '{{ addslashes($class->title) }}', '{{ addslashes($class->subject) }}', '{{ addslashes($class->time) }}', '{{ addslashes($class->room) }}')"
                                            class="text-gray-500 hover:text-red-600 p-2 rounded-xl hover:bg-red-50 transition" 
                                            title="Edit Class Details">
                                        <i class="fa-regular fa-pen-to-square text-lg"></i>
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

    <!-- ==================== FACULTY PROFILE MODAL (WITH PHOTO UPLOADER) ==================== -->
    <div id="facultyProfileModal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 border-2 border-red-300 shadow-2xl relative animate-in fade-in zoom-in duration-150">
            
            <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                <!-- Interactive Profile Avatar with Upload Click -->
                <div class="flex items-center gap-4">
                    <div class="relative group cursor-pointer" onclick="document.getElementById('profile_picture_input').click()" title="Click to change photo">
                        <div class="w-14 h-14 rounded-full overflow-hidden bg-red-600 text-white flex items-center justify-center font-black text-xl border-2 border-red-400 shadow-md">
                            <img id="avatar_preview_img" 
                                 src="{{ ($avatarPath && file_exists(public_path($avatarPath))) ? asset($avatarPath) : '' }}" 
                                 alt="Avatar" 
                                 class="{{ ($avatarPath && file_exists(public_path($avatarPath))) ? 'block' : 'hidden' }} w-full h-full object-cover">
                            
                            <span id="avatar_initial_span" class="{{ ($avatarPath && file_exists(public_path($avatarPath))) ? 'hidden' : 'block' }}">
                                {{ strtoupper(substr($user->first_name ?? 'F', 0, 1)) }}
                            </span>
                        </div>
                        
                        <!-- Camera overlay badge -->
                        <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-150">
                            <i class="fa-solid fa-camera text-white text-sm"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 bg-white text-red-600 w-5 h-5 rounded-full border border-gray-200 flex items-center justify-center text-[10px] shadow-xs">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-base font-black text-gray-900">Faculty Profile & Settings</h3>
                        <p class="text-xs text-gray-500 font-medium">Southern Isabela Academy &bull; SHS Department</p>
                        <button type="button" 
                                onclick="document.getElementById('profile_picture_input').click()" 
                                class="text-[11px] font-bold text-red-600 hover:underline mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-upload text-[10px]"></i> Change Profile Picture
                        </button>
                    </div>
                </div>

                <button type="button" onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-700 text-xl p-1">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data" class="space-y-4 mt-4">
                @csrf
                
                <!-- Hidden file input for Avatar -->
                <input type="file" 
                       id="profile_picture_input" 
                       name="profile_picture" 
                       accept="image/*" 
                       class="hidden" 
                       onchange="previewProfileImage(event)">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ $user->first_name }}" required
                               class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ $user->last_name }}" required
                               class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                               class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Contact Phone</label>
                        <input type="text" name="phone_number" value="{{ $user->phone_number }}"
                               placeholder="e.g. 09123456789"
                               class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    </div>
                </div>

                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                    <p class="text-xs font-bold text-gray-800">Change Password (leave blank if unchanged)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="password" name="password" placeholder="New Password"
                                   class="w-full text-xs font-semibold px-4 py-2 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none bg-white">
                        </div>
                        <div>
                            <input type="password" name="password_confirmation" placeholder="Confirm Password"
                                   class="w-full text-xs font-semibold px-4 py-2 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeProfileModal()" 
                            class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-black shadow-md transition">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== EDIT CLASS MODAL ==================== -->
    <div id="editClassModal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 border-2 border-red-300 shadow-2xl relative animate-in fade-in zoom-in duration-150">
            <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-red-600 rounded-full"></span>
                    <h3 class="text-lg font-black text-gray-900">Edit Class Session Details</h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 text-xl p-1">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('teacher.schedule.update') }}" class="space-y-4 mt-4">
                @csrf
                <input type="hidden" name="class_id" id="edit_class_id">

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Class Title & Section</label>
                    <input type="text" name="title" id="edit_title" required
                           class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Subject Area</label>
                    <input type="text" name="subject" id="edit_subject" required
                           class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Scheduled Time</label>
                        <input type="text" name="time" id="edit_time" required
                               placeholder="e.g. 8:00 AM - 9:30 AM"
                               class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Room / Venue</label>
                        <input type="text" name="room" id="edit_room" required
                               placeholder="e.g. Computer Lab 1"
                               class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-black shadow-md transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openProfileModal() {
            const modal = document.getElementById('facultyProfileModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeProfileModal() {
            const modal = document.getElementById('facultyProfileModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Live Profile Image Preview
        function previewProfileImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('avatar_preview_img');
                    const initial = document.getElementById('avatar_initial_span');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    img.classList.add('block');
                    if (initial) {
                        initial.classList.add('hidden');
                        initial.classList.remove('block');
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function openEditModal(id, title, subject, time, room) {
            document.getElementById('edit_class_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_subject').value = subject;
            document.getElementById('edit_time').value = time;
            document.getElementById('edit_room').value = room;

            const modal = document.getElementById('editClassModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditModal() {
            const modal = document.getElementById('editClassModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

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