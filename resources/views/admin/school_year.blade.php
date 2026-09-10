@extends('layouts.app')

@section('title', 'Report Generation - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar (Spaced Left and Vertical) -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-base shadow-xs shrink-0">
                <i class="fa-solid fa-chart-column text-amber-300"></i>
            </div>
            <div>
                
        <!-- Status Messages -->
        @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- Main Horizontal Layout -->
        <div class="flex flex-col xl:flex-row items-start justify-between gap-8 pt-4 pb-8">
            
            <!-- 1. Left: Icon, Title & Dynamic Badge -->
            <div class="flex items-center gap-3 pt-2">
                <div class="w-11 h-11 rounded-2xl bg-[#8b1818] text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Academic Year Management</h1>
                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-[11px] font-black rounded-full uppercase tracking-wider">
                    A.Y. {{ $activeSchoolYear ?? '2028-2029' }}
                </span>
            </div>

            <!-- 2. Middle: Form Inputs -->
            <div class="w-full max-w-xs">
                <form id="syUpdateForm" action="{{ route('admin.school-year.update') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-600 mb-1.5">SCHOOL YEAR</label>
                        <input type="text" id="sy_input" name="academic_year" value="{{ $activeSchoolYear ?? '2028-2029' }}" required class="w-full bg-slate-50 border border-slate-200/90 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8b1818]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-600 mb-1.5">SEMESTER</label>
                        <select id="sem_input" name="semester" class="w-full bg-slate-50 border border-slate-200/90 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8b1818]">
                            <option value="1st Semester" {{ ($activeSemester ?? '') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ ($activeSemester ?? '') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer" {{ ($activeSemester ?? '') == 'Summer' ? 'selected' : '' }}>Summer</option>
                        </select>
                    </div>

                    <input type="hidden" name="admin_password" id="update_admin_password">

                    <button type="button" onclick="triggerSecurityModal('update')" class="w-full py-2.5 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer mt-2">
                        SAVE & SET ACTIVE PERIOD
                    </button>
                </form>
            </div>

            <!-- 3. Right: New Academic Year Reset Card -->
            <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm w-full max-w-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-sm mb-3">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 mb-4">New Academic Year Reset</h3>
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs mb-5">
                        <span class="text-slate-500 font-bold">Attendance Logs to Clear:</span>
                        <span class="font-black text-red-600">{{ $totalLogs ?? 0 }} logs</span>
                    </div>
                </div>

                <form id="syResetForm" action="{{ route('admin.school-year.reset') }}" method="POST">
                    @csrf
                    <input type="hidden" name="admin_password" id="reset_admin_password">
                    <button type="button" onclick="triggerSecurityModal('reset')" class="w-full py-3 rounded-xl bg-[#dc2626] hover:bg-[#b91c1c] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer">
                        RESET ATTENDANCE LOGS
                    </button>
                </form>
            </div>

        </div>
        <div class="border-b border-slate-100 my-4"></div>

        <!-- Admin Password Confirmation Modal -->
        <div id="securityConfirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: #ffffff; border-radius: 1.5rem; width: 100%; max-width: 440px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                
                <div style="padding: 1.5rem 1.5rem 1rem; text-align: center;">
                    <div id="modalWarningIconBox" style="width: 50px; height: 50px; border-radius: 9999px; margin: 0 auto 1rem; display: flex; items-center; justify-content: center;">
                        <i id="modalWarningIcon" class="text-xl"></i>
                    </div>
                    <h3 id="modalSecTitle" style="font-size: 1.1rem; font-weight: 900; color: #1e293b; margin-bottom: 0.5rem;">Security Confirmation</h3>
                    <p id="modalSecMessage" style="font-size: 0.75rem; color: #64748b; line-height: 1.5;"></p>
                </div>

                <div style="padding: 0 1.5rem 1.25rem;">
                    <label style="display: block; font-size: 10px; font-weight: 900; text-transform: uppercase; color: #475569; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Admin Password Verification</label>
                    <input type="password" id="modalSecPasswordInput" placeholder="Enter your current password..." style="width: 100%; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.65rem 0.85rem; font-size: 0.8rem; outline: none;">
                    <p id="modalPasswordError" style="display: none; color: #dc2626; font-size: 11px; font-weight: 700; margin-top: 0.4rem;">Please enter your password to proceed.</p>
                </div>

                <div style="padding: 0.85rem 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeSecurityModal()" style="padding: 0.5rem 1rem; border-radius: 0.75rem; background: #e2e8f0; color: #475569; border: none; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="button" id="confirmActionBtn" onclick="submitAuthorizedAction()" style="padding: 0.5rem 1.25rem; border-radius: 0.75rem; color: #ffffff; border: none; font-size: 0.75rem; font-weight: 900; cursor: pointer;">
                        Confirm & Proceed
                    </button>
                </div>

            </div>
        </div>

        <script>
        let currentSecAction = null;

        function triggerSecurityModal(actionType) {
            currentSecAction = actionType;
            const modal = document.getElementById('securityConfirmModal');
            const title = document.getElementById('modalSecTitle');
            const msg = document.getElementById('modalSecMessage');
            const iconBox = document.getElementById('modalWarningIconBox');
            const icon = document.getElementById('modalWarningIcon');
            const confirmBtn = document.getElementById('confirmActionBtn');
            const passInput = document.getElementById('modalSecPasswordInput');
            const err = document.getElementById('modalPasswordError');

            passInput.value = '';
            err.style.display = 'none';

            if (actionType === 'update') {
                const targetAY = document.getElementById('sy_input').value;
                const targetSem = document.getElementById('sem_input').value;

                title.innerText = "Confirm Academic Period Change";
                msg.innerText = "Babaguhin mo ang aktibong taon patungong " + targetAY + " (" + targetSem + "). Maaapektuhan nito ang lahat ng dashboard at realtime reporting.";
                iconBox.style.background = "#fef3c7";
                icon.className = "fa-solid fa-triangle-exclamation";
                icon.style.color = "#d97706";
                confirmBtn.style.background = "#8b1818";
                confirmBtn.innerText = "Authorize Period Change";
            } else if (actionType === 'reset') {
                title.innerText = "CRITICAL: Reset Attendance Logs";
                msg.innerText = "PERMANENTENG mabubura ang lahat ng naitalang attendance scan logs mula sa Gate Kiosk para magsimula sa bagong school year.";
                iconBox.style.background = "#fee2e2";
                icon.className = "fa-solid fa-skull-crossbones";
                icon.style.color = "#dc2626";
                confirmBtn.style.background = "#dc2626";
                confirmBtn.innerText = "Authorize & Clear Logs";
            }

            modal.style.display = 'flex';
            setTimeout(() => passInput.focus(), 50);
        }

        function closeSecurityModal() {
            const modal = document.getElementById('securityConfirmModal');
            if (modal) modal.style.display = 'none';
            currentSecAction = null;
        }

        function submitAuthorizedAction() {
            const pass = document.getElementById('modalSecPasswordInput').value.trim();
            const err = document.getElementById('modalPasswordError');

            if (!pass) {
                err.innerText = "Password is required to confirm this action.";
                err.style.display = 'block';
                return;
            }

            if (currentSecAction === 'update') {
                document.getElementById('update_admin_password').value = pass;
                document.getElementById('syUpdateForm').submit();
            } else if (currentSecAction === 'reset') {
                document.getElementById('reset_admin_password').value = pass;
                document.getElementById('syResetForm').submit();
            }
        }

        document.getElementById('modalSecPasswordInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitAuthorizedAction();
            } else if (e.key === 'Escape') {
                closeSecurityModal();
            }
        });
        </script>
    </main>
</div>
@endsection