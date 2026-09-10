@extends('layouts.app')

@section('title', 'Admin Dashboard - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    @include('admin.dashboard.partials.header')

    <!-- Main Content Container -->
    <main class="pt-10 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-8 flex-1">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-xs font-bold rounded-2xl flex items-center justify-between shadow-xs w-full">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 text-sm cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <!-- Section 1: KPI Summary Metrics -->
        @include('admin.dashboard.partials.stats-cards')

        <!-- Section 2: At-Risk Student Alert Widget -->
        @include('admin.dashboard.partials.risk-alerts')

        <!-- Section 3: Real-Time Attendance Stream -->
        @include('admin.dashboard.partials.attendance-feed')

    </main>
</div>

<!-- All Modals (Profile & Metrics Summary) -->
@include('admin.dashboard.partials.modals')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
document.addEventListener("DOMContentLoaded", function() {
    function loadCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(loadCharts, 50);
            return;
        }

        const chartOpts = {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            elements: { point: { radius: 0 } }
        };

        const s = document.getElementById('studentSparkline');
        if (s && !s.dataset.init) {
            s.dataset.init = "1";
            new Chart(s, {
                type: 'line',
                data: {
                    labels: ['1','2','3','4','5','6','7'],
                    datasets: [{ data: [0, 0, 1, 1, 1, 1, 1], borderColor: '#f59e0b', backgroundColor: 'rgba(245, 158, 11, 0.12)', borderWidth: 1.5, fill: true, tension: 0.3 }]
                },
                options: chartOpts
            });
        }

        const a = document.getElementById('attendanceSparkline');
        if (a && !a.dataset.init) {
            a.dataset.init = "1";
            new Chart(a, {
                type: 'line',
                data: {
                    labels: ['M','T','W','T','F'],
                    datasets: [{ data: [0, 0, 0, 0, 0], borderColor: '#e11d48', backgroundColor: 'rgba(225, 29, 72, 0.12)', borderWidth: 1.5, fill: true, tension: 0.3 }]
                },
                options: chartOpts
            });
        }

        const e = document.getElementById('evalSparkline');
        if (e && !e.dataset.init) {
            e.dataset.init = "1";
            new Chart(e, {
                type: 'line',
                data: {
                    labels: ['1','2','3','4'],
                    datasets: [{ data: [0, 0, 0, 0], borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.12)', borderWidth: 1.5, fill: true, tension: 0.3 }]
                },
                options: chartOpts
            });
        }

        const sms = document.getElementById('smsSparkline');
        if (sms && !sms.dataset.init) {
            sms.dataset.init = "1";
            new Chart(sms, {
                type: 'bar',
                data: {
                    labels: ['1','2','3','4','5','6'],
                    datasets: [{ data: [0, 0, 0, 0, 0, 0], backgroundColor: 'rgba(16, 185, 129, 0.5)', borderRadius: 2 }]
                },
                options: chartOpts
            });
        }
    }
    loadCharts();
});

function openMetricModal(type) {
    // Redirect directly to the dedicated analytics summary report page route
    window.location.href = "{{ url('admin/analytics') }}/" + type;
}

function closeMetricModal() {
    // No longer needed for page routing, but kept as a safe fallback
    const overlay = document.getElementById('metricModalOverlay');
    if (overlay) overlay.style.display = 'none';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMetricModal();
});

function bindDashboardCards() {
    const targets = [
        { key: 'TOTAL STUDENTS', type: 'students' },
        { key: 'ATTENDANCE RATE', type: 'attendance' },
        { key: 'FACULTY EVALUATION', type: 'evaluation' },
        { key: 'SMS SENT TODAY', type: 'sms' }
    ];

    targets.forEach(t => {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
        let node;
        while (node = walker.nextNode()) {
            if (node.nodeValue && node.nodeValue.trim().toUpperCase() === t.key) {
                const card = node.parentElement.closest('.bg-white');
                if (card && !card.dataset.metricBound) {
                    card.dataset.metricBound = "true";
                    card.style.cursor = "pointer";
                    card.style.transition = "transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease";
                    
                    card.addEventListener('mouseenter', () => {
                        card.style.transform = "translateY(-3px)";
                        card.style.boxShadow = "0 10px 25px -5px rgba(0, 0, 0, 0.08)";
                        card.style.borderColor = "#8b1818";
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.transform = "none";
                        card.style.boxShadow = "";
                        card.style.borderColor = "";
                    });
                    card.addEventListener('click', (e) => {
                        e.preventDefault();
                        openMetricModal(t.type);
                    });
                }
                break;
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindDashboardCards);
} else {
    bindDashboardCards();
}
</script>
@endsection

@push('scripts')
<script>
    function openEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function togglePasswordVisibility(fieldId, btn) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            field.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }

    function filterFeedTable() {
        const searchInput = document.getElementById('feedSearch');
        if (!searchInput) return;

        const query = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.tap-row');

        rows.forEach(row => {
            const name = row.querySelector('.student-name')?.innerText.toLowerCase() || '';
            const id = row.querySelector('.student-id')?.innerText.toLowerCase() || '';
            const placement = row.querySelector('.student-placement')?.innerText.toLowerCase() || '';

            if (name.includes(query) || id.includes(query) || placement.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('editProfileModal');
        
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeEditProfileModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeEditProfileModal();
            }
        });

        @if($errors->any())
            openEditProfileModal();
        @endif
    });
</script>
@endpush