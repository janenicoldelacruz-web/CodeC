<!-- ================= Modal: Edit Admin Profile ================= -->
<div id="editProfileModal" 
     class="fixed inset-0 z-50 bg-slate-950/60 hidden items-center justify-center p-4 sm:p-6"
     style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border-2 border-slate-200 overflow-hidden max-h-[92vh] flex flex-col">
        
        <div class="px-8 py-5 border-b-2 border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-[#590d0d] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                    <i class="fa-solid fa-user-gear text-amber-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Administrator Profile</h3>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">Manage your administrative credentials and personal details</p>
                </div>
            </div>
            <button type="button" onclick="closeEditProfileModal()" class="w-9 h-9 rounded-xl hover:bg-slate-200/70 text-slate-400 hover:text-slate-700 flex items-center justify-center transition focus:outline-none cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" id="adminProfileForm" class="p-8 overflow-y-auto space-y-6">
            @csrf
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-wider">Profile Information</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">First Name <span class="text-red-600">*</span></label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required placeholder="First Name"
                                    class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Last Name <span class="text-red-600">*</span></label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-user"></i></span>
                            <input type="text" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required placeholder="Last Name"
                                    class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Employee / Admin ID</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-hashtag"></i></span>
                            <input type="text" name="id_number" value="{{ old('id_number', auth()->user()->id_number) }}" placeholder="ADM-2026-001"
                                    class="w-full py-2.5 px-3 text-sm font-mono font-bold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Contact Number</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-phone"></i></span>
                            <input type="text" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" placeholder="09171234567"
                                    class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Institutional Email Address <span class="text-red-600">*</span></label>
                    <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                        <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required placeholder="admin@siatrack.edu.ph"
                                class="w-full py-2.5 px-3 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-wider">Authentication & Security</span>
                    <span class="text-[10px] font-bold text-slate-400 italic">Leave empty to keep current password</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Current Password</label>
                    <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                        <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-key"></i></span>
                        <input type="password" id="current_password_field" name="current_password" placeholder="Required only if updating password"
                                class="w-full py-2.5 px-3 pr-10 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                        <button type="button" onclick="togglePasswordVisibility('current_password_field', this)" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">New Password</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" id="new_password_field" name="password" minlength="8" placeholder="Minimum 8 characters"
                                    class="w-full py-2.5 px-3 pr-10 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                            <button type="button" onclick="togglePasswordVisibility('new_password_field', this)" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Confirm New Password</label>
                        <div class="relative flex items-center rounded-xl border border-slate-300 focus-within:border-[#590d0d] bg-white">
                            <span class="pl-3.5 text-slate-400 text-xs"><i class="fa-solid fa-lock-open"></i></span>
                            <input type="password" id="confirm_password_field" name="password_confirmation" minlength="8" placeholder="Re-enter new password"
                                    class="w-full py-2.5 px-3 pr-10 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                            <button type="button" onclick="togglePasswordVisibility('confirm_password_field', this)" class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t-2 border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditProfileModal()" 
                        class="px-5 py-2.5 rounded-xl border-2 border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-extrabold cursor-pointer">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#590d0d] hover:bg-[#460a0a] text-white text-xs font-black shadow-md shadow-red-950/20 flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- METRIC DETAIL MODAL CONTAINER -->
@php
    $modalStudents = \Illuminate\Support\Facades\DB::table('users')
        ->where('role_id', 3)
        ->select('id', 'first_name', 'last_name', 'email', 'id_number', 'created_at')
        ->latest('created_at')->take(8)->get();

    $modalAttendance = \Illuminate\Support\Facades\Schema::hasTable('attendance_logs')
        ? \Illuminate\Support\Facades\DB::table('attendance_logs')
            ->leftJoin('users', 'attendance_logs.user_id', '=', 'users.id')
            ->select('attendance_logs.*', 'users.first_name', 'users.last_name', 'users.id_number')
            ->whereDate('attendance_logs.created_at', now()->today())
            ->latest('attendance_logs.created_at')->take(8)->get()
        : collect();

    $modalFaculty = \Illuminate\Support\Facades\DB::table('users')
        ->where('role_id', 2)
        ->select('id', 'first_name', 'last_name', 'email', 'id_number')
        ->take(8)->get();
@endphp

<div id="metricModalOverlay" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-200">
        
        <!-- Modal Header with Correct IDs -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
            <div class="flex items-center gap-3">
                <div id="modalIconBox" class="w-10 h-10 rounded-2xl flex items-center justify-center text-base"></div>
                <div>
                    <h3 id="modalTitleText" class="text-base font-black text-slate-800"></h3>
                    <p id="modalSubText" class="text-xs text-slate-400 font-medium"></p>
                </div>
            </div>
            <button onclick="closeMetricModal()" class="w-8 h-8 rounded-full bg-slate-200/60 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 overflow-y-auto flex-1 space-y-4">
            
            <!-- 1. Students Content -->
            <div id="sec_students" class="metric-sec" style="display: none;">
                <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="font-bold text-slate-500">Enrolled Students (Recent)</span>
                    <span class="font-black text-[#590d0d]">{{ count($modalStudents) }} student(s) loaded</span>
                </div>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Student Name</th>
                                <th class="p-3">LRN / ID</th>
                                <th class="p-3">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($modalStudents as $st)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-800">{{ $st->first_name }} {{ $st->last_name }}</td>
                                <td class="p-3 text-slate-500 font-mono text-[11px]">{{ $st->id_number ?? 'N/A' }}</td>
                                <td class="p-3 text-slate-500">{{ $st->email }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-400">No enrolled students found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Attendance Content -->
            <div id="sec_attendance" class="metric-sec" style="display: none;">
                <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="font-bold text-slate-500">Today's Gate Tap Records</span>
                    <span class="font-black text-red-600">{{ count($modalAttendance) }} tap(s) today</span>
                </div>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Student</th>
                                <th class="p-3">Time In</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($modalAttendance as $at)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-800">{{ $at->first_name ?? 'Student' }} {{ $at->last_name ?? '' }}</td>
                                <td class="p-3 text-slate-500">{{ \Carbon\Carbon::parse($at->created_at)->format('h:i A') }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ ($at->status ?? '') == 'LATE' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $at->status ?? 'PRESENT' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-400">No attendance taps recorded today.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Faculty Evaluation Content -->
            <div id="sec_evaluation" class="metric-sec" style="display: none;">
                <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="font-bold text-slate-500">Faculty Members & Performance</span>
                    <span class="font-black text-blue-600">{{ count($modalFaculty) }} instructors</span>
                </div>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Instructor</th>
                                <th class="p-3">School ID</th>
                                <th class="p-3">Evaluation Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($modalFaculty as $fa)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-800">{{ $fa->first_name }} {{ $fa->last_name }}</td>
                                <td class="p-3 text-slate-500 font-mono text-[11px]">{{ $fa->id_number ?? 'FAC-N/A' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">Active Cycle</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-4 text-center text-slate-400">No instructors found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. SMS Alerts Content -->
            <div id="sec_sms" class="metric-sec" style="display: none;">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-900 text-xs space-y-2">
                    <div class="flex items-center justify-between font-bold">
                        <span>GSM / SMS Notification Gateway</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-black">ONLINE</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">Automatically dispatches SMS alerts to parents whenever an NFC scan is logged at the gate kiosk.</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between font-bold text-slate-600">
                        <span>Dispatched Today:</span>
                        <span class="font-black text-slate-800">{{ $activeSMS ?? 0 }} alerts</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-600">
                        <span>Delivery Success Rate:</span>
                        <span class="font-black text-emerald-600">100%</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/60 flex justify-end">
            <button onclick="closeMetricModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition cursor-pointer">
                Close
            </button>
        </div>
    </div>
</div>