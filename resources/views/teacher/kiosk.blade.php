<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIATRACK - Live NFC Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            70% { transform: scale(1); box-shadow: 0 0 0 30px rgba(255, 255, 255, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-[#8b1818] via-[#5c0f0f] to-[#1a0404] text-white min-h-screen flex flex-col justify-between p-6 select-none overflow-hidden">

    <!-- Top Bar with Professional Action Controls & Audio Toggle -->
    <header class="flex items-center justify-between border-b border-white/10 pb-4 px-2 w-full">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 bg-white/10 border border-white/20 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <span class="font-black text-sm tracking-wider">SIA</span>
            </div>
            <div>
                <h1 class="text-xl font-black tracking-wider text-white">SIATRACK NFC KIOSK</h1>
                <p class="text-[11px] text-red-200/75 font-semibold">Southern Isabela Academy &bull; Attendance Terminal</p>
            </div>
        </div>

        <div class="flex items-center gap-5">
            <!-- Clock & Date -->
            <div class="text-right">
                <p id="live_clock" class="text-xl font-black font-mono text-white tracking-widest">--:--:-- --</p>
                <p id="live_date" class="text-xs text-red-200/75 font-semibold">------ --, ----</p>
            </div>

            <!-- Header Controls (Audio Mute, Fullscreen & Exit) -->
            <div class="flex items-center gap-2 pl-4 border-l border-white/15">
                <button type="button" onclick="toggleAudio()" id="audio_toggle_btn" title="Toggle Sound FX" class="w-10 h-10 rounded-xl bg-black/30 hover:bg-black/50 border border-white/20 text-white flex items-center justify-center transition cursor-pointer shadow-md backdrop-blur-md">
                    <i class="fa-solid fa-volume-high text-sm text-red-300" id="audio_icon"></i>
                </button>
                <button type="button" onclick="toggleFullscreen()" title="Toggle Fullscreen" class="w-10 h-10 rounded-xl bg-black/30 hover:bg-black/50 border border-white/20 text-white flex items-center justify-center transition cursor-pointer shadow-md backdrop-blur-md">
                    <i class="fa-solid fa-expand text-sm text-red-300"></i>
                </button>
                <a href="{{ route('teacher.attendance') }}" title="Exit Kiosk" class="w-10 h-10 rounded-xl bg-black/30 hover:bg-black/50 border border-white/20 text-white flex items-center justify-center transition cursor-pointer shadow-md backdrop-blur-md">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm text-red-300"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Center Scanner / Student Display Box -->
    <main class="w-full px-4 my-auto text-center py-4 space-y-4">
        
        <!-- Default Standby State with Recent Scans Ticker -->
        <div id="standby_box" class="space-y-5">
            <div class="relative w-40 h-40 mx-auto flex items-center justify-center">
                <div class="w-36 h-36 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center pulse-ring backdrop-blur-sm">
                    <i class="fa-solid fa-wifi rotate-45 text-4xl text-white"></i>
                </div>
            </div>

            <div class="bg-black/30 border border-white/15 backdrop-blur-xl rounded-3xl p-6 max-w-lg mx-auto shadow-2xl space-y-2">
                <h2 class="text-2xl font-black tracking-tight text-white">READY TO SCAN</h2>
                <p class="text-xs text-red-100/80 font-medium">Please hold your SIA NFC Student ID near the ACR122U Reader.</p>

                <!-- Hidden/Testing input to capture physical/manual reader input -->
                <div class="pt-1 max-w-xs mx-auto">
                    <input type="text" id="sim_uid_input" placeholder="Awaiting card tap..." autofocus
                           class="w-full bg-black/50 border border-white/25 rounded-xl px-4 py-2 text-xs text-center font-mono text-white placeholder-white/30 focus:border-white outline-none shadow-inner">
                </div>

                <!-- Recent Scan Ticker Feed -->
                <div class="mt-4 pt-3 border-t border-white/10 text-left">
                    <p class="text-[10px] font-black uppercase tracking-wider text-red-200/60 mb-1.5 flex items-center justify-between">
                        <span>Recent Scan Feed</span>
                        <span class="text-[9px] text-emerald-400 font-mono">Live Ticker</span>
                    </p>
                    <div id="recent_scans_list" class="space-y-1 text-xs font-medium text-red-100/90 max-h-20 overflow-y-auto">
                        <div class="flex items-center justify-between text-white/40 italic text-[11px]">
                            <span>Waiting for initial student tap...</span>
                            <span class="font-mono text-[10px]">--:--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Result Card (Real Database Data) -->
        <div id="result_box" class="hidden bg-black/70 border-2 border-red-500/60 rounded-3xl p-8 shadow-2xl backdrop-blur-2xl max-w-2xl mx-auto text-left transition-all duration-300 transform scale-100">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div class="flex items-center gap-2 text-emerald-400 font-black text-xs">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span id="res_action_label">SUCCESSFUL ATTENDANCE LOG</span>
                </div>
                <span id="res_status_badge" class="bg-emerald-500 text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-md">
                    ON-TIME
                </span>
            </div>

            <!-- Student Name & Info Display Area -->
            <div class="flex items-center gap-6 mt-6">
                <div id="res_avatar_container" class="w-32 h-32 rounded-2xl overflow-hidden bg-black/40 border-2 border-red-500/40 flex items-center justify-center shrink-0 shadow-xl">
                    <i class="fa-regular fa-user text-5xl text-white/50"></i>
                </div>
                <div class="space-y-1.5">
                    <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-red-400 bg-red-950/70 border border-red-500/30 px-3 py-0.5 rounded-md">Verified Student</span>
                    <h3 id="res_student_name" class="text-3xl font-black text-white tracking-tight leading-tight">Student Name</h3>
                    <p id="res_id_number" class="text-xs font-mono text-red-200/90 font-bold">ID: 00000</p>
                    <p id="res_strand" class="text-xs text-red-300/80 font-semibold">Grade 11 - STEM</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-6 pt-4 border-t border-white/10 bg-black/40 p-4 rounded-2xl">
                <div>
                    <p class="text-[10px] text-red-200/60 font-bold uppercase tracking-wider">Time In Recorded</p>
                    <p id="res_time_in" class="text-lg font-mono font-black text-white mt-0.5">--:-- --</p>
                </div>
                <div>
                    <p class="text-[10px] text-red-200/60 font-bold uppercase tracking-wider">Track / Section</p>
                    <p id="res_section" class="text-xs font-semibold text-red-100 mt-1">General Track</p>
                </div>
            </div>

            <!-- Confirm & Next Button -->
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-end">
                <button type="button" onclick="saveAttendanceRecord()" class="w-full py-3 rounded-xl bg-gradient-to-r from-red-600 to-[#8b1818] hover:from-red-500 hover:to-red-700 text-white font-black text-xs uppercase tracking-wider transition shadow-lg cursor-pointer flex items-center justify-center gap-2 border border-red-400/30">
                    <i class="fa-solid fa-floppy-disk text-sm"></i> Confirm & Next Student
                </button>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="text-center text-xs text-red-200/60 border-t border-white/10 pt-3 flex flex-col items-center justify-center gap-0.5 font-semibold px-2 w-full">
        <span>Southern Isabela Academy &bull; SIATRACK Attendance Management System</span>
        <span class="text-[10px] text-red-300/40">Powered by Web & Mobile Application Development &bull; All Rights Reserved &copy; 2026</span>
    </footer>

    <!-- Audio Beep FX -->
    <audio id="beep_success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        // Real-Time Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('live_clock').innerText = now.toLocaleTimeString();
            document.getElementById('live_date').innerText = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        setInterval(updateClock, 1000);
        updateClock();

        let lastProcessedTag = '';
        let resetTimer = null;
        let soundEnabled = true;
        let recentScans = [];

        // Toggle Audio Chime
        function toggleAudio() {
            soundEnabled = !soundEnabled;
            const icon = document.getElementById('audio_icon');
            icon.className = soundEnabled ? "fa-solid fa-volume-high text-sm text-red-300" : "fa-solid fa-volume-xmark text-sm text-slate-400";
        }

        // Toggle Fullscreen Function
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Trigger Tap Function (Communicates with NfcAttendanceController)
        async function triggerTap(uid) {
            if (!uid) return;

            try {
                const response = await fetch('/api/nfc/tap', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ tag_id: uid })
                });

                const data = await response.json();

                if (data.success) {
                    // Play Sound if enabled
                    if (soundEnabled) {
                        document.getElementById('beep_success').play().catch(() => {});
                    }

                    // Push to Recent Scans Ticker Feed
                    recentScans.unshift({
                        name: data.student.name,
                        time: data.time,
                        status: data.status
                    });
                    if (recentScans.length > 3) recentScans.pop();
                    updateRecentScansTicker();

                    // Populate Real Student Details from Database
                    document.getElementById('res_student_name').innerText = data.student.name;
                    document.getElementById('res_id_number').innerText = 'ID: ' + data.student.id_number;
                    document.getElementById('res_strand').innerText = data.student.grade_level + ' - ' + data.student.track;
                    document.getElementById('res_time_in').innerText = data.time;
                    document.getElementById('res_section').innerText = data.student.section;
                    document.getElementById('res_action_label').innerText = data.action === 'TIME OUT' ? 'TIME OUT RECORDED' : 'SUCCESSFUL ATTENDANCE LOG';
                    
                    // Status Badge Styling
                    const badge = document.getElementById('res_status_badge');
                    badge.innerText = data.status;
                    badge.className = data.status === 'ON-TIME' 
                        ? 'bg-emerald-500 text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-md'
                        : 'bg-amber-400 text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider shadow-md';

                    // Student Avatar / Photo
                    const avatarBox = document.getElementById('res_avatar_container');
                    if (data.student.avatar) {
                        avatarBox.innerHTML = `<img src="${data.student.avatar}" class="w-full h-full object-cover">`;
                    } else {
                        avatarBox.innerHTML = `<span class="text-4xl font-black text-red-400">${data.student.name.charAt(0)}</span>`;
                    }

                    // Show Success Result Card & Hide Standby View
                    document.getElementById('standby_box').classList.add('hidden');
                    document.getElementById('result_box').classList.remove('hidden');

                    if (resetTimer) clearTimeout(resetTimer);

                    // Auto-reset back to standby after 5 seconds
                    resetTimer = setTimeout(() => {
                        resetToStandby();
                    }, 5000);

                } else {
                    alert(data.message || 'Unrecognized Card');
                }
            } catch (err) {
                console.error('Tap request failed:', err);
            }
        }

        // Update Recent Scans Ticker UI
        function updateRecentScansTicker() {
            const listContainer = document.getElementById('recent_scans_list');
            if (!listContainer) return;

            let html = '';
            recentScans.forEach(scan => {
                html += `
                    <div class="flex items-center justify-between bg-black/30 px-2.5 py-1 rounded-lg border border-white/5">
                        <span class="font-bold text-white truncate max-w-[150px]">${scan.name}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] px-1.5 py-0.5 rounded font-black ${scan.status === 'ON-TIME' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300'}">${scan.status}</span>
                            <span class="font-mono text-[10px] text-red-200/60">${scan.time}</span>
                        </div>
                    </div>
                `;
            });
            listContainer.innerHTML = html;
        }

        // Manual Confirm Button Trigger
        function saveAttendanceRecord() {
            if (resetTimer) clearTimeout(resetTimer);
            resetToStandby();
        }

        function resetToStandby() {
            if (resetTimer) clearTimeout(resetTimer);
            document.getElementById('result_box').classList.add('hidden');
            document.getElementById('standby_box').classList.remove('hidden');
            document.getElementById('sim_uid_input').value = '';
            document.getElementById('sim_uid_input').focus();
        }

        // Real-Time Hardware Polling Loop (Checks for physical NFC taps every 1 second)
        async function pollLatestTap() {
            try {
                const res = await fetch('/api/nfc/latest-tap');
                const data = await res.json();
                
                if (data.tag_id && data.tag_id !== lastProcessedTag) {
                    lastProcessedTag = data.tag_id;
                    triggerTap(data.tag_id);
                }
            } catch (e) {
                // Silent catch for background polling
            }
        }

        setInterval(pollLatestTap, 1000);

        // Manual Keyboard / Reader Input Support
        document.getElementById('sim_uid_input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const uid = this.value.trim();
                if (uid) {
                    lastProcessedTag = uid;
                    triggerTap(uid);
                    this.value = '';
                }
            }
        });
    </script>
</body>
</html>