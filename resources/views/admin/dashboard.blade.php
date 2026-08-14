<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIATRACK</title>
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
        $avatarPath = $user->profile_picture ?? session('admin_avatar_' . $user->id);
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
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm transition">
                    <i class="fa-solid fa-gauge text-sm"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-users text-sm"></i>
                    <span>User Management</span>
                </a>

                <a href="{{ route('admin.attendance') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-clipboard-user text-sm"></i>
                    <span>Attendance Records</span>
                </a>

                <a href="{{ route('admin.evaluations') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                    <span>Faculty Evaluation Mgmt</span>
                </a>

                <a href="{{ route('admin.reports') }}" 
                   class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                    <i class="fa-solid fa-file-waveform text-sm"></i>
                    <span>Report Generation</span>
                </a>
            </nav>
        </div>

        <!-- Admin Profile Footer Button -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/60">
            <div class="flex items-center justify-between">
                <button type="button" onclick="openAdminProfileModal()" class="flex items-center gap-3 text-left group">
                    <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-red-200 group-hover:border-red-500 transition shrink-0 bg-red-100 flex items-center justify-center">
                        @if($avatarPath && file_exists(public_path($avatarPath)))
                            <img src="{{ asset($avatarPath) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <span class="font-black text-sm text-red-600">
                                {{ strtoupper(substr($user->first_name ?? 'A', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-gray-800 truncate group-hover:text-red-600 transition">
                            {{ $user->first_name ?? 'Administrator' }} {{ $user->last_name ?? '' }}
                        </p>
                        <p class="text-[10px] text-red-600 font-bold uppercase tracking-wider">System Admin</p>
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
        
        <!-- Header -->
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Admin Dashboard</h1>

            <!-- Functional Admin Profile Trigger & Bell -->
            <div class="flex items-center gap-4">
                <button type="button" 
                        onclick="openAdminProfileModal()" 
                        class="flex items-center gap-2.5 text-sm font-bold text-gray-900 hover:text-red-600 cursor-pointer transition bg-gray-50 px-3.5 py-1.5 rounded-full border border-gray-200 hover:border-red-300">
                    <div class="w-6 h-6 rounded-full overflow-hidden bg-red-100 flex items-center justify-center text-[11px] font-bold text-red-600 shrink-0">
                        @if($avatarPath && file_exists(public_path($avatarPath)))
                            <img src="{{ asset($avatarPath) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->first_name ?? 'A', 0, 1)) }}
                        @endif
                    </div>
                    <span>Admin Profile</span>
                </button>

                <button type="button" 
                        onclick="alert('No new administrative alerts.')"
                        class="relative text-gray-800 hover:text-red-600 transition text-lg p-1.5 rounded-full hover:bg-gray-100">
                    <i class="fa-regular fa-bell"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-600 rounded-full"></span>
                </button>
            </div>
        </header>

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

            <!-- KPI SUMMARY CARDS (Figure 13) -->
            <section class="space-y-3">
                <h2 class="text-base font-extrabold text-gray-900">KPI Summary Cards</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Students -->
                    <div class="bg-white border-2 border-red-300 rounded-3xl p-5 relative shadow-xs flex flex-col justify-between">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full border-2 border-gray-900 flex items-center justify-center text-gray-800 text-lg">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-900">{{ $totalStudents ?? '550' }}</p>
                                <p class="text-xs text-gray-500 font-bold">Total Students Enrolled</p>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Attendance Rate -->
                    <div class="bg-white border-2 border-red-300 rounded-3xl p-5 relative shadow-xs flex flex-col justify-between">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                        <div class="flex items-center gap-3">
                            <div class="border border-red-300 rounded-lg p-1.5 text-center w-12 h-12 flex flex-col items-center justify-center bg-gray-50">
                                <span class="text-xs font-black text-red-600">{{ date('d') }}</span>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-900">94.7%</p>
                                <p class="text-xs text-gray-500 font-bold">Daily Attendance Rate</p>
                            </div>
                        </div>
                    </div>

                    <!-- Faculty Evaluation Progress -->
                    <div class="bg-white border-2 border-red-300 rounded-3xl p-5 relative shadow-xs flex flex-col justify-between">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 border border-red-200 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-900">78%</p>
                                <p class="text-xs text-gray-500 font-bold">Evaluation Progress</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active SMS Today -->
                    <div class="bg-white border-2 border-red-300 rounded-3xl p-5 relative shadow-xs flex flex-col justify-between">
                        <div class="absolute -top-3 left-6 bg-white px-3 border-t-2 border-x-2 border-red-300 rounded-t-lg h-3"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-comment-sms"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-900">{{ $activeSMS ?? '112' }}</p>
                                <p class="text-xs text-gray-500 font-bold">Active SMS Today</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- REAL-TIME ACTIVITY FEED (Figure 13) -->
            <section class="space-y-3">
                <div class="bg-white border-2 border-red-300 rounded-3xl p-6 shadow-xs relative">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-extrabold text-gray-900">
                            Real-Time Activity Feed: Live Attendance Stream
                        </h2>
                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-bold">
                            Live Sync Active
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-white font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4">Student Name</th>
                                    <th class="py-3 px-4 text-center">Grade / Section</th>
                                    <th class="py-3 px-4 text-center">Time In</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 font-medium text-gray-900 bg-[#f3f4f6]">
                                <tr class="hover:bg-gray-200 transition">
                                    <td class="py-2.5 px-4 font-bold">Jane Nicol Dela Cruz</td>
                                    <td class="py-2.5 px-4 text-center">12 - STEM A</td>
                                    <td class="py-2.5 px-4 text-center font-bold">7:15 AM</td>
                                    <td class="py-2.5 px-4 text-center"><span class="bg-green-500 text-white px-3 py-0.5 rounded-full text-[10px] font-black">On-Time</span></td>
                                </tr>
                                <tr class="hover:bg-gray-200 transition">
                                    <td class="py-2.5 px-4 font-bold">Ceazar Muaa Rodolfo</td>
                                    <td class="py-2.5 px-4 text-center">11 - HUMSS B</td>
                                    <td class="py-2.5 px-4 text-center font-bold">7:18 AM</td>
                                    <td class="py-2.5 px-4 text-center"><span class="bg-green-500 text-white px-3 py-0.5 rounded-full text-[10px] font-black">On-Time</span></td>
                                </tr>
                                <tr class="hover:bg-gray-200 transition">
                                    <td class="py-2.5 px-4 font-bold">Marco Manlapaz</td>
                                    <td class="py-2.5 px-4 text-center">12 - ABM A</td>
                                    <td class="py-2.5 px-4 text-center font-bold">7:25 AM</td>
                                    <td class="py-2.5 px-4 text-center"><span class="bg-green-500 text-white px-3 py-0.5 rounded-full text-[10px] font-black">On-Time</span></td>
                                </tr>
                                <tr class="hover:bg-gray-200 transition">
                                    <td class="py-2.5 px-4 font-bold">Eliza Peralta</td>
                                    <td class="py-2.5 px-4 text-center">11 - GAS A</td>
                                    <td class="py-2.5 px-4 text-center font-bold">7:30 AM</td>
                                    <td class="py-2.5 px-4 text-center"><span class="bg-green-500 text-white px-3 py-0.5 rounded-full text-[10px] font-black">On-Time</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end gap-2 mt-5">
                        <span class="w-7 h-3.5 bg-[#881337] rounded-sm slanted-pill"></span>
                        <span class="w-7 h-3.5 bg-[#881337] rounded-sm slanted-pill"></span>
                        <span class="w-7 h-3.5 bg-[#881337] rounded-sm slanted-pill"></span>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <!-- ==================== ADMIN PROFILE MODAL ==================== -->
    <div id="adminProfileModal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 border-2 border-red-300 shadow-2xl relative animate-in fade-in zoom-in duration-150">
            
            <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <!-- Interactive Photo Click -->
                    <div class="relative group cursor-pointer" onclick="document.getElementById('admin_picture_input').click()" title="Click to upload profile photo">
                        <div class="w-14 h-14 rounded-full overflow-hidden bg-red-600 text-white flex items-center justify-center font-black text-xl border-2 border-red-400 shadow-md">
                            <img id="admin_preview_img" 
                                 src="{{ ($avatarPath && file_exists(public_path($avatarPath))) ? asset($avatarPath) : '' }}" 
                                 alt="Avatar" 
                                 class="{{ ($avatarPath && file_exists(public_path($avatarPath))) ? 'block' : 'hidden' }} w-full h-full object-cover">
                            
                            <span id="admin_initial_span" class="{{ ($avatarPath && file_exists(public_path($avatarPath))) ? 'hidden' : 'block' }}">
                                {{ strtoupper(substr($user->first_name ?? 'A', 0, 1)) }}
                            </span>
                        </div>
                        
                        <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-150">
                            <i class="fa-solid fa-camera text-white text-sm"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 bg-white text-red-600 w-5 h-5 rounded-full border border-gray-200 flex items-center justify-center text-[10px] shadow-xs">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-base font-black text-gray-900">Administrator Profile & Settings</h3>
                        <p class="text-xs text-gray-500 font-medium">Southern Isabela Academy &bull; System Administration</p>
                        <button type="button" 
                                onclick="document.getElementById('admin_picture_input').click()" 
                                class="text-[11px] font-bold text-red-600 hover:underline mt-0.5 flex items-center gap-1">
                            <i class="fa-solid fa-upload text-[10px]"></i> Change Profile Picture
                        </button>
                    </div>
                </div>

                <button type="button" onclick="closeAdminProfileModal()" class="text-gray-400 hover:text-gray-700 text-xl p-1">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-4 mt-4">
                @csrf
                
                <!-- Hidden file input for Photo -->
                <input type="file" 
                       id="admin_picture_input" 
                       name="profile_picture" 
                       accept="image/*" 
                       class="hidden" 
                       onchange="previewAdminImage(event)">

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
                    <button type="button" onclick="closeAdminProfileModal()" 
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

    <!-- Scripts -->
    <script>
        function openAdminProfileModal() {
            const modal = document.getElementById('adminProfileModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAdminProfileModal() {
            const modal = document.getElementById('adminProfileModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function previewAdminImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('admin_preview_img');
                    const initial = document.getElementById('admin_initial_span');
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
    </script>
</body>
</html>