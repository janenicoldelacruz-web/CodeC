<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Attendance - Teacher Portal - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sia-card { background: #ffffff; border: 2px solid #e2e8f0; border-radius: 24px; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .sia-card:hover { border-color: #cbd5e1; transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
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
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Student Attendance Monitor</h1>
                <p class="text-xs text-slate-500 font-bold mt-0.5">Southern Isabela Academy &bull; Real-Time NFC Tap Logs & Records</p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Profile Trigger Button -->
                <button type="button" onclick="openProfileModal()" class="flex items-center gap-3 pl-4 border-l-2 border-slate-200 hover:opacity-80 transition cursor-pointer">
                    @if(!empty($teacher->photo))
                        <img src="{{ asset('storage/' . $teacher->photo) }}" class="w-10 h-10 rounded-2xl object-cover border-2 border-amber-300 shadow-2xs">
                    @else
                        <div class="w-10 h-10 rounded-2xl bg-amber-100 border border-amber-300 text-amber-900 flex items-center justify-center font-black text-xs">
                            {{ strtoupper(substr($teacher->first_name ?? 'T', 0, 1)) }}{{ strtoupper(substr($teacher->last_name ?? 'F', 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-extrabold text-slate-900 leading-tight">Prof. {{ $teacher->first_name }} {{ $teacher->last_name }}</span>
                        <span class="text-[10px] font-bold text-amber-700">Edit Faculty Profile <i class="fa-solid fa-angle-right text-[8px]"></i></span>
                    </div>
                </button>
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

            <!-- 1. LIVE SUMMARY METRIC CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Present Card -->
                <div class="sia-card p-6 flex items-center gap-5 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 translate-x-4 -translate-y-4 w-24 h-24 bg-emerald-50 rounded-full blur-xl group-hover:bg-emerald-100 transition"></div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-xl font-bold shrink-0 shadow-inner">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Present Today</p>
                        <h3 class="text-3xl font-black text-slate-900 mt-0.5 font-mono">0</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md mt-1 inline-block">On-Time Logging</span>
                    </div>
                </div>

                <!-- Late Card -->
                <div class="sia-card p-6 flex items-center gap-5 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 translate-x-4 -translate-y-4 w-24 h-24 bg-amber-50 rounded-full blur-xl group-hover:bg-amber-100 transition"></div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-xl font-bold shrink-0 shadow-inner">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Late Arrivals</p>
                        <h3 class="text-3xl font-black text-slate-900 mt-0.5 font-mono">0</h3>
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md mt-1 inline-block">Grace Period Passed</span>
                    </div>
                </div>

                <!-- Total Scanned Card -->
                <div class="sia-card p-6 flex items-center gap-5 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 translate-x-4 -translate-y-4 w-24 h-24 bg-red-50 rounded-full blur-xl group-hover:bg-red-100 transition"></div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 border border-red-200 text-[#8b1818] flex items-center justify-center text-xl font-bold shrink-0 shadow-inner">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] uppercase font-black text-slate-400 tracking-wider">Total Scanned</p>
                        <h3 class="text-3xl font-black text-slate-900 mt-0.5 font-mono">0</h3>
                        <span class="text-[10px] font-bold text-[#8b1818] bg-red-50 px-2 py-0.5 rounded-md mt-1 inline-block">Total Daily Taps</span>
                    </div>
                </div>
            </div>

            <!-- EMPHASIZED PROFESSIONAL LAUNCH NFC KIOSK BANNER -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#8b1818] via-[#6f1212] to-[#3d0808] text-white p-8 md:p-10 shadow-2xl border border-red-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 group">
                <div class="absolute -right-12 -top-12 w-72 h-72 bg-red-500/10 rounded-full blur-3xl pointer-events-none transition-all duration-500 group-hover:bg-red-500/20"></div>
                <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-black/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-start md:items-center gap-6 relative z-10">
                    <div class="w-18 h-18 min-w-[4.5rem] min-h-[4.5rem] rounded-2xl bg-black/30 border border-white/15 flex items-center justify-center text-3xl text-amber-300 shadow-inner backdrop-blur-md">
                        <i class="fa-solid fa-nfc-symbol animate-pulse"></i>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-[10px] font-extrabold uppercase px-3 py-1 rounded-full tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Terminal Online
                            </span>
                            <span class="text-xs font-mono text-red-200/60">&bull; ACR122U Reader Ready</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-black tracking-tight text-white">Live NFC Attendance Terminal</h2>
                        <p class="text-xs md:text-sm text-red-100/75 font-medium max-w-2xl leading-relaxed">
                            Launch the dedicated fullscreen kiosk station for automated student identification, instant audio-visual feedback, and real-time database logging.
                        </p>
                    </div>
                </div>

                <div class="relative z-10 shrink-0 w-full md:w-auto flex justify-end">
                    <a href="{{ route('teacher.kiosk') }}" 
                       class="w-full md:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-white hover:bg-slate-50 text-[#8b1818] font-black text-xs uppercase tracking-wider shadow-xl transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer">
                        <i class="fa-solid fa-laptop-code text-sm"></i>
                        <span>Launch Kiosk Terminal</span>
                        <i class="fa-solid fa-arrow-right text-[10px] opacity-60 ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- FILTER & SEARCH TOOLBAR -->
<div class="sia-card p-6">
    <form method="GET" action="{{ route('teacher.attendance') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        
        <!-- Search Input -->
        <div class="lg:col-span-2">
            <label class="block text-xs font-black text-slate-700 mb-1">Search Student</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or ID number..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-slate-50/50">
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-xs font-black text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-white">
                <option value="">All Statuses</option>
                <option value="ON-TIME" {{ request('status') === 'ON-TIME' ? 'selected' : '' }}>On-Time</option>
                <option value="LATE" {{ request('status') === 'LATE' ? 'selected' : '' }}>Late</option>
            </select>
        </div>

        <!-- Date From -->
        <div>
            <label class="block text-xs font-black text-slate-700 mb-1">From Date</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-white">
        </div>

        <!-- Date To & Action Buttons -->
        <div>
            <label class="block text-xs font-black text-slate-700 mb-1">To Date</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-white">
        </div>

        <div class="lg:col-span-5 flex items-center justify-between pt-2 border-t border-slate-100 mt-2">
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#731414] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-filter text-xs"></i> Apply Filters
                </button>
                <a href="{{ route('teacher.attendance') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black transition">
                    Reset
                </a>
            </div>

            <!-- Export CSV Button -->
            <a href="{{ route('teacher.attendance.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'status' => request('status'), 'search' => request('search')]) }}" 
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-xs"></i> Export Report (CSV)
            </a>
        </div>
    </form>
</div>

            <!-- Attendance Dashboard Section -->
            <div class="sia-card p-6 md:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-200 text-[#8b1818] flex items-center justify-center text-base shadow-inner">
                            <i class="fa-solid fa-clipboard-user"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900 tracking-tight">Live Attendance Feed</h2>
                            <p class="text-xs text-slate-400 font-bold mt-0.5">Real-time student tap-in feed and daily records log</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-600">
                            <i class="fa-solid fa-calendar-days text-slate-400"></i>
                            <span>{{ date('F d, Y') }}</span>
                        </div>
                        <span class="inline-flex items-center gap-2 text-xs font-black text-[#8b1818] bg-red-50 border border-red-200 px-4 py-2 rounded-xl shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span> Active Feed
                        </span>
                    </div>
                </div>

                <!-- Placeholder / Attendance Feed Table Area -->
                <div class="py-20 text-center">
                    <div class="w-20 h-20 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200 text-slate-300 flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-800">Awaiting Student NFC Taps</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-1 max-w-sm mx-auto">Launched kiosk terminal is ready. Scanned student attendance logs will appear here instantly in real-time.</p>
                </div>
            </div>

        </div>
    </main>

    <!-- ================= EDIT FACULTY PROFILE MODAL ================= -->
    <div id="teacher_profile_modal" class="hidden fixed inset-0 items-center justify-center p-4 sm:p-6 bg-slate-950/60 transition-all duration-200 z-50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden transform transition-all p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-100 text-[#8b1818] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Edit Faculty Profile</h3>
                        <p class="text-xs text-slate-400 font-bold">Update your institutional registration details</p>
                    </div>
                </div>
                <button type="button" onclick="closeProfileModal()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Faculty / Employee ID *</label>
                        <input type="text" name="id_number" value="{{ old('id_number', $teacher->id_number) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-slate-100 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none bg-white">
                            <option value="">Select Gender</option>
                            <option value="1" {{ (int)$teacher->gender === 1 ? 'selected' : '' }}>Male</option>
                            <option value="2" {{ (int)$teacher->gender === 2 ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Contact Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $teacher->phone_number) }}" placeholder="09xxxxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Password *</label>
                        <input type="password" name="password" placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                        <span class="block text-[10px] text-slate-400 mt-0.5">Leave blank to keep current</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-bold focus:border-[#8b1818] outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="relative shrink-0">
                        @if(!empty($teacher->photo))
                            <img id="profile_preview" src="{{ asset('storage/' . $teacher->photo) }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-300 shadow-sm">
                        @else
                            <div id="profile_preview_fallback" class="w-16 h-16 rounded-2xl bg-amber-100 border border-amber-300 text-amber-900 flex items-center justify-center font-black text-xl">
                                {{ strtoupper(substr($teacher->first_name ?? 'T', 0, 1)) }}{{ strtoupper(substr($teacher->last_name ?? 'F', 0, 1)) }}
                            </div>
                            <img id="profile_preview" class="w-16 h-16 rounded-2xl object-cover border-2 border-slate-300 shadow-sm hidden">
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 mb-1">Faculty Profile Photo</label>
                        <input type="file" name="photo" accept="image/jpeg,image/png" onchange="previewImage(event)" 
                               class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-[#8b1818] file:text-white hover:file:bg-[#731414] cursor-pointer">
                        <span class="block text-[10px] text-slate-400 mt-1 font-medium">Optional, JPG/PNG up to 2MB</span>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeProfileModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#731414] text-white text-xs font-black uppercase tracking-wider transition shadow-md shadow-red-950/20 active:scale-[0.98] cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SCRIPT -->
    <script>
        function openProfileModal() {
            const modal = document.getElementById('teacher_profile_modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeProfileModal() {
            const modal = document.getElementById('teacher_profile_modal');
            if (modal) {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('profile_preview');
                const fallback = document.getElementById('profile_preview_fallback');
                if (preview) {
                    preview.src = reader.result;
                    preview.classList.remove('hidden');
                }
                if (fallback) fallback.classList.add('hidden');
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>