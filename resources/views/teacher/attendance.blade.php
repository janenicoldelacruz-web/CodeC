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
        .sia-card { background: #ffffff; border: 1.5px solid #f1f5f9; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
        .cal-day { aspect-ratio: 1; border-radius: 12px; transition: all 0.15s ease; cursor: pointer; }
        .cal-day:hover:not(.disabled) { transform: translateY(-2px); border-color: #f59e0b; }
        .cal-day.active { background: #5c0d11 !important; color: #ffffff !important; border-color: #f59e0b !important; box-shadow: 0 4px 12px rgba(92, 13, 17, 0.35); }
        .cal-day.active .cal-dot { background-color: #f59e0b !important; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex">

    <!-- REUSABLE MAROON SIDEBAR -->
    @include('layouts.sidebar')

    <!-- MAIN DASHBOARD CONTENT (Offset for 280px sidebar) -->
    <div style="margin-left: 288px;" class="flex-1 min-h-screen p-8 bg-[#f8fafc]">
        
        <!-- Header Profile Bar -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200/60">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Student Attendance Monitor</h1>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Southern Isabela Academy &bull; Real-Time NFC Tap Logs & Attendance Calendar</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 border border-amber-300 flex items-center justify-center font-bold text-amber-900 text-xs">
                    {{ substr(Auth::user()->first_name ?? 'P', 0, 1) }}{{ substr(Auth::user()->last_name ?? 'G', 0, 1) }}
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">{{ Auth::user()->first_name ?? 'Prof.' }} {{ Auth::user()->last_name ?? 'Teacher' }}</div>
                    <div class="text-[10px] text-amber-700 font-semibold">Faculty Member</div>
                </div>
            </div>
        </div>

        <!-- Metric Counter Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="sia-card p-5 flex items-center justify-between border-l-4 border-l-emerald-500">
                <div>
                    <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Present On Date</p>
                    <h3 id="stat_present" class="text-3xl font-black text-slate-900 mt-1">0</h3>
                    <span class="text-[11px] font-semibold text-emerald-600 mt-1 inline-block">On-Time & Present</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>

            <div class="sia-card p-5 flex items-center justify-between border-l-4 border-l-amber-500">
                <div>
                    <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Late Arrivals</p>
                    <h3 id="stat_late" class="text-3xl font-black text-slate-900 mt-1">0</h3>
                    <span class="text-[11px] font-semibold text-amber-600 mt-1 inline-block">Tardy / Grace Period</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>

            <div class="sia-card p-5 flex items-center justify-between border-l-4 border-l-rose-500">
                <div>
                    <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">Absent Students</p>
                    <h3 id="stat_absent" class="text-3xl font-black text-slate-900 mt-1">0</h3>
                    <span class="text-[11px] font-semibold text-rose-600 mt-1 inline-block">Unlogged / Excused</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
            </div>
        </div>

        <!-- Live NFC Kiosk Launcher Banner -->
        <div class="rounded-3xl bg-gradient-to-r from-[#5c0d11] to-[#8b1818] p-6 text-white shadow-xl mb-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-red-950/20">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-black/30 border border-white/20 flex items-center justify-center text-amber-300 text-2xl shrink-0">
                    <i class="fa-solid fa-wifi rotate-45"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            TERMINAL ONLINE &bull; READY
                        </span>
                    </div>
                    <h3 class="text-xl font-black tracking-wide">Live NFC Attendance Terminal</h3>
                    <p class="text-xs text-red-100/75 mt-0.5">Launch the dedicated station for tap identification and real-time database recording.</p>
                </div>
            </div>
            <a href="{{ route('teacher.kiosk') }}" class="px-6 py-3.5 bg-white text-slate-950 hover:bg-amber-300 rounded-2xl font-black text-xs uppercase tracking-wider transition shadow-lg shrink-0 flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Launch Kiosk Terminal
            </a>
        </div>

        <!-- Interactive Attendance Calendar & Records Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            <!-- Calendar Card (4 Columns) -->
            <div class="lg:col-span-5 sia-card p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-[#5c0d11] text-base"></i>
                        <h3 id="cal_month_year" class="font-black text-slate-900 text-sm tracking-tight">September 2026</h3>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="prevMonth()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <button type="button" onclick="nextMonth()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Calendar Week Header -->
                <div class="grid grid-cols-7 gap-1 text-center mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Su</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">Mo</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">Tu</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">We</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">Th</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">Fr</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">Sa</span>
                </div>

                <!-- Calendar Days Grid (Populated dynamically) -->
                <div id="calendar_days" class="grid grid-cols-7 gap-1.5 text-center text-xs font-bold text-slate-700">
                    <!-- Javascript populates days here -->
                </div>

                <!-- Legend Indicator -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-semibold">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>High Tap Log</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#5c0d11]"></span>
                        <span>Selected Day</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                        <span>Weekend / Empty</span>
                    </div>
                </div>
            </div>

            <!-- Attendance Records by Date Table (7 Columns) -->
            <div class="lg:col-span-7 sia-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200 uppercase tracking-wider">Filtered View</span>
                                <h3 id="selected_date_label" class="font-black text-slate-900 text-base">September 17, 2026</h3>
                            </div>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Students logged under your class sections on this date</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <select id="filter_status_select" onchange="filterTableByStatus()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 text-xs font-semibold rounded-xl text-slate-700 outline-none">
                                <option value="ALL">All Statuses</option>
                                <option value="PRESENT">Present</option>
                                <option value="LATE">Late</option>
                                <option value="ABSENT">Absent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Students Status Table -->
                    <div class="overflow-x-auto max-h-[380px] overflow-y-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50/80 sticky top-0 border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th class="py-2.5 px-3">Student</th>
                                    <th class="py-2.5 px-3">LRN / ID</th>
                                    <th class="py-2.5 px-3">Section</th>
                                    <th class="py-2.5 px-3">Time In</th>
                                    <th class="py-2.5 px-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody id="student_attendance_body" class="divide-y divide-slate-100">
                                <!-- Populated dynamically based on clicked calendar date -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between text-xs text-slate-400 font-medium">
                    <span id="showing_count_label">Loading attendance...</span>
                    <button type="button" onclick="exportCurrentDateReport()" class="font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-excel"></i> Export Selected Date
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Frontend Dynamic Calendar Script -->
    <script>
        let currentDate = new Date(2026, 8, 17); // Default to Sept 17, 2026
        let selectedDay = 17;
        let selectedMonth = 8; // 0-indexed: 8 is September
        let selectedYear = 2026;

        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        // Dummy database state simulation (Real students list)
        const mockStudentList = [
            { id: "STD-2026-001", name: "Villanueva, Mark", section: "Grade 11 - STEM", time: "07:18 AM", status: "PRESENT" },
            { id: "STD-2026-002", name: "Reyes, Angelica", section: "Grade 12 - ABM", time: "07:22 AM", status: "PRESENT" },
            { id: "STD-2026-003", name: "Aquino, Christian", section: "Grade 11 - TVL", time: "07:46 AM", status: "LATE" },
            { id: "STD-2026-004", name: "Gambito, Joshua", section: "Grade 11 - STEM", time: "07:12 AM", status: "PRESENT" },
            { id: "STD-2026-005", name: "Bautista, Sarah", section: "Grade 12 - HUMSS", time: "--:-- --", status: "ABSENT" },
            { id: "STD-2026-006", name: "Dela Cruz, Juan", section: "Grade 11 - STEM", time: "--:-- --", status: "ABSENT" }
        ];

        function renderCalendar() {
            const firstDay = new Date(selectedYear, selectedMonth, 1).getDay();
            const daysInMonth = new Date(selectedYear, selectedMonth + 1, 0).getDate();

            document.getElementById('cal_month_year').innerText = `${months[selectedMonth]} ${selectedYear}`;

            const daysContainer = document.getElementById('calendar_days');
            daysContainer.innerHTML = '';

            // Empty cells before day 1
            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement('div');
                empty.className = 'p-2 text-slate-300 cal-day disabled';
                daysContainer.appendChild(empty);
            }

            // Days of the month
            for (let d = 1; d <= daysInMonth; d++) {
                const dayEl = document.createElement('div');
                const isSelected = (d === selectedDay);
                dayEl.className = `cal-day flex flex-col items-center justify-center p-1.5 border border-slate-100 ${isSelected ? 'active' : 'bg-slate-50/50 hover:bg-slate-100'}`;
                
                dayEl.innerHTML = `
                    <span class="text-xs font-bold leading-none">${d}</span>
                    <span class="cal-dot w-1.5 h-1.5 rounded-full mt-1 ${d % 2 === 0 ? 'bg-emerald-500' : 'bg-transparent'}"></span>
                `;

                dayEl.onclick = () => selectDay(d);
                daysContainer.appendChild(dayEl);
            }
        }

        function selectDay(day) {
            selectedDay = day;
            renderCalendar();
            loadAttendanceForDate(selectedYear, selectedMonth, day);
        }

        function prevMonth() {
            selectedMonth--;
            if (selectedMonth < 0) {
                selectedMonth = 11;
                selectedYear--;
            }
            selectedDay = 1;
            renderCalendar();
            loadAttendanceForDate(selectedYear, selectedMonth, selectedDay);
        }

        function nextMonth() {
            selectedMonth++;
            if (selectedMonth > 11) {
                selectedMonth = 0;
                selectedYear++;
            }
            selectedDay = 1;
            renderCalendar();
            loadAttendanceForDate(selectedYear, selectedMonth, selectedDay);
        }

        function loadAttendanceForDate(year, month, day) {
            const dateStr = `${months[month]} ${day}, ${year}`;
            document.getElementById('selected_date_label').innerText = dateStr;

            // Compute randomized simulation base on the day for realistic feel
            let presentCount = 0;
            let lateCount = 0;
            let absentCount = 0;

            const tbody = document.getElementById('student_attendance_body');
            tbody.innerHTML = '';

            // Check if weekend
            const dayOfWeek = new Date(year, month, day).getDay();
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 font-semibold italic">
                            <i class="fa-solid fa-mug-hot text-2xl mb-1 text-slate-300 block"></i>
                            Weekend / No Classes Scheduled on this date.
                        </td>
                    </tr>
                `;
                document.getElementById('stat_present').innerText = 0;
                document.getElementById('stat_late').innerText = 0;
                document.getElementById('stat_absent').innerText = 0;
                document.getElementById('showing_count_label').innerText = '0 records found';
                return;
            }

            mockStudentList.forEach((s, idx) => {
                let status = s.status;
                let time = s.time;

                // Alternate dummy status on specific date clicks
                if (day % 3 === 0 && idx === 1) { status = "LATE"; time = "07:38 AM"; }
                if (day === 1 && idx === 4) { status = "PRESENT"; time = "07:15 AM"; }

                if (status === 'PRESENT') presentCount++;
                if (status === 'LATE') lateCount++;
                if (status === 'ABSENT') absentCount++;

                const row = document.createElement('tr');
                row.className = "hover:bg-slate-50 transition";
                row.innerHTML = `
                    <td class="py-3 px-3 font-bold text-slate-900">${s.name}</td>
                    <td class="py-3 px-3 font-mono text-[11px] text-slate-500">${s.id}</td>
                    <td class="py-3 px-3 text-slate-600 font-medium">${s.section}</td>
                    <td class="py-3 px-3 font-mono font-bold ${status === 'ABSENT' ? 'text-slate-300' : 'text-slate-700'}">${time}</td>
                    <td class="py-3 px-3 text-right">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${
                            status === 'PRESENT' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                            (status === 'LATE' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200')
                        }">${status}</span>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('stat_present').innerText = presentCount;
            document.getElementById('stat_late').innerText = lateCount;
            document.getElementById('stat_absent').innerText = absentCount;
            document.getElementById('showing_count_label').innerText = `Showing ${mockStudentList.length} student records`;
        }

        function filterTableByStatus() {
            const filterVal = document.getElementById('filter_status_select').value;
            const rows = document.querySelectorAll('#student_attendance_body tr');

            rows.forEach(r => {
                if (filterVal === 'ALL') {
                    r.style.display = '';
                } else {
                    const statusText = r.querySelector('td:last-child span')?.innerText.trim();
                    r.style.display = (statusText === filterVal) ? '' : 'none';
                }
            });
        }

        function exportCurrentDateReport() {
            alert(`Exporting official CSV attendance report for ${document.getElementById('selected_date_label').innerText}...`);
        }

        // Initialize on Load
        renderCalendar();
        loadAttendanceForDate(selectedYear, selectedMonth, selectedDay);
    </script>
</body>
</html>