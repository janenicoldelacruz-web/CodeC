<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - SIATRACK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#fcfbfb] text-gray-800 antialiased min-h-screen flex">

    <!-- ==================== SIDEBAR ==================== -->
    @if(view()->exists('layouts.sidebar'))
        @include('layouts.sidebar')
    @else
        <aside class="w-64 bg-white border-r border-red-100 flex flex-col justify-between shrink-0 h-screen sticky top-0">
            <div>
                <div class="p-6 pb-5 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black tracking-tight text-[#b91c1c]">SIATRACK</span>
                        <i class="fa-solid fa-wifi rotate-45 text-[#b91c1c] text-lg"></i>
                    </div>
                    <p class="text-[11px] font-semibold text-gray-400 mt-0.5">Southern Isabela Academy</p>
                </div>
                <nav class="p-4 space-y-2 text-sm font-semibold">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                        <i class="fa-solid fa-gauge text-sm"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 bg-[#cf2e2e] text-white rounded-2xl shadow-sm transition">
                        <i class="fa-solid fa-users text-sm"></i>
                        <span>User Management</span>
                    </a>
                    <a href="{{ route('admin.attendance') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-2xl transition">
                        <i class="fa-solid fa-clipboard-user text-sm"></i>
                        <span>Attendance Records</span>
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
    @endif

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="flex-1 flex flex-col min-w-0">
        
        <!-- Header -->
        <header class="bg-white border-b border-red-100 px-8 py-4 flex items-center justify-between sticky top-0 z-20 shadow-xs">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">User Management</h1>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.create') }}" 
                   class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i> Add New User
                </a>
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl w-full">

            <!-- Alerts -->
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

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-2xs">
                    <p class="text-xs font-bold text-gray-500">Total Users</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-2xs">
                    <p class="text-xs font-bold text-gray-500">Students</p>
                    <p class="text-2xl font-black text-blue-600 mt-1">{{ $studentCount }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-2xs">
                    <p class="text-xs font-bold text-gray-500">Faculty / Teachers</p>
                    <p class="text-2xl font-black text-red-600 mt-1">{{ $teacherCount }}</p>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-2xs">
                    <p class="text-xs font-bold text-gray-500">Active Accounts</p>
                    <p class="text-2xl font-black text-green-600 mt-1">{{ $activeUsers }}</p>
                </div>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white border-2 border-red-200 rounded-3xl p-6 shadow-xs">
                
                <!-- Table Controls (Search & Filter) -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center justify-between gap-4 mb-5">
                    <div class="flex items-center gap-2">
                        <select name="role" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 border border-gray-300 rounded-xl outline-none focus:border-red-500">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-red-600 pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search ID, Name, Email..." 
                               class="w-full pl-9 pr-4 py-2 text-xs border border-gray-300 rounded-full focus:border-red-500 focus:outline-none">
                    </div>
                </form>

                <!-- Users Table -->
                <div class="overflow-x-auto rounded-2xl border border-gray-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white font-bold uppercase tracking-wider">
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">ID Number</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Contact</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 font-medium text-gray-800 bg-white">
                            @forelse($users as $u)
                                <tr class="hover:bg-red-50/40 transition">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-bold flex items-center justify-center text-xs shrink-0">
                                                {{ strtoupper(substr($u->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $u->first_name }} {{ $u->last_name }}</p>
                                                <p class="text-[11px] text-gray-500">{{ $u->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-gray-700">
                                        {{ $u->id_number ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider 
                                            {{ $u->role_id == 1 ? 'bg-purple-100 text-purple-700' : ($u->role_id == 2 ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                            {{ $u->role->name ?? 'User' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600">
                                        {{ $u->phone_number ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $u->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                            <span class="w-2 h-2 rounded-full {{ $u->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                            {{ $u->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- RESET PASSWORD TRIGGER BUTTON -->
                                            <button type="button" 
                                                    onclick="openResetPasswordModal('{{ $u->id }}', '{{ addslashes($u->first_name . ' ' . $u->last_name) }}', '{{ $u->id_number ?? $u->email }}')"
                                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" 
                                                    title="Reset Password">
                                                <i class="fa-solid fa-key text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        No user accounts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $users->links() }}
                </div>

            </div>

        </div>
    </main>

    <!-- ==================== RESET PASSWORD MODAL ==================== -->
    <div id="resetPasswordModal" class="fixed inset-0 z-50 bg-black/60 hidden items-center justify-center p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 border-2 border-red-300 shadow-2xl relative animate-in fade-in zoom-in duration-150">
            
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Reset User Password</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Southern Isabela Academy &bull; Admin Control</p>
                    </div>
                </div>
                <button type="button" onclick="closeResetPasswordModal()" class="text-gray-400 hover:text-gray-700 text-xl p-1">
                    &times;
                </button>
            </div>

            <!-- Target User Info Banner -->
            <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-2xl">
                <p class="text-[11px] text-gray-500 font-semibold uppercase">Target Account:</p>
                <p id="target_user_name" class="text-sm font-black text-gray-900 mt-0.5">User Name</p>
                <p id="target_user_id" class="text-xs text-red-700 font-mono font-bold">ID: 00000</p>
            </div>

            <form id="resetPasswordForm" method="POST" action="" class="space-y-4 mt-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" id="modal_new_password" required placeholder="Enter new password (min. 6 chars)"
                           class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="modal_confirm_password" required placeholder="Confirm new password"
                           class="w-full text-xs font-semibold px-4 py-2.5 rounded-xl border border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none">
                </div>

                <!-- Quick Preset Button -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <span class="text-gray-500 text-[11px]">Quick Action:</span>
                    <button type="button" onclick="setPresetPassword('siatrack2026')" class="text-red-600 font-bold hover:underline">
                        Set to Default ("siatrack2026")
                    </button>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeResetPasswordModal()" 
                            class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-bold transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openResetPasswordModal(userId, userName, userIdentifier) {
            document.getElementById('target_user_name').innerText = userName;
            document.getElementById('target_user_id').innerText = userIdentifier;
            
            // Set dynamic form action URL
            document.getElementById('resetPasswordForm').action = "/admin/users/" + userId + "/reset-password";
            
            // Clear inputs
            document.getElementById('modal_new_password').value = '';
            document.getElementById('modal_confirm_password').value = '';

            const modal = document.getElementById('resetPasswordModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeResetPasswordModal() {
            const modal = document.getElementById('resetPasswordModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function setPresetPassword(pwd) {
            document.getElementById('modal_new_password').value = pwd;
            document.getElementById('modal_confirm_password').value = pwd;
        }
    </script>
</body>
</html>