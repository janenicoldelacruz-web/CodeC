@extends('layouts.app')

@section('title', 'Admin Dashboard - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    @include('admin.dashboard.partials.header')

    <!-- Main Content Container (Standardized Responsive Padding) -->
    <main class="py-8 px-4 sm:px-6 lg:px-10 w-full space-y-8 flex-1">

        <!-- Section 1: KPI Summary Metrics -->
        @include('admin.dashboard.partials.stats-cards')

        <!-- Section 2: At-Risk Student Alert Widget -->
        @include('admin.dashboard.partials.risk-alerts')

        <!-- Section 3: Real-Time Attendance Stream -->
        @include('admin.dashboard.partials.attendance-feed')

    </main>
</div>

<!-- All Modals (Profile, Success Alert, & Metrics Summary) -->
@include('admin.dashboard.partials.modals')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // 1. Sparkline Charts Initialization
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

        const sparklines = [
            { id: 'studentSparkline', type: 'line', data: [0, 0, 1, 1, 1, 1, 1], border: '#f59e0b', bg: 'rgba(245, 158, 11, 0.12)' },
            { id: 'attendanceSparkline', type: 'line', data: [0, 0, 0, 0, 0], border: '#e11d48', bg: 'rgba(225, 29, 72, 0.12)' },
            { id: 'evalSparkline', type: 'line', data: [0, 0, 0, 0], border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.12)' }
        ];

        sparklines.forEach(s => {
            const el = document.getElementById(s.id);
            if (el && !el.dataset.init) {
                el.dataset.init = "1";
                new Chart(el, {
                    type: s.type,
                    data: {
                        labels: s.data.map((_, i) => (i + 1).toString()),
                        datasets: [{
                            data: s.data,
                            borderColor: s.border || 'transparent',
                            backgroundColor: s.bg,
                            borderWidth: s.border ? 1.5 : 0,
                            fill: s.type === 'line',
                            tension: 0.3,
                            borderRadius: s.type === 'bar' ? 2 : 0
                        }]
                    },
                    options: chartOpts
                });
            }
        });
    }
    loadCharts();
    bindDashboardCards();
});

function openMetricModal(type) {
    window.location.href = "{{ url('admin/analytics') }}/" + type;
}

function closeMetricModal() {
    const overlay = document.getElementById('metricModalOverlay');
    if (overlay) overlay.style.display = 'none';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMetricModal();
});

function bindDashboardCards() {
    const targets = [
        { key: 'TOTAL STUDENTS', type: 'students' },
        { key: 'FACULTY EVALUATION', type: 'evaluation' }
    ];

    // FIXED: Changed cardTargets to targets
    targets.forEach(t => {
        let card = document.querySelector(`[data-metric="${t.type}"]`);

        if (!card) {
            const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
            let node;
            while (node = walker.nextNode()) {
                if (node.nodeValue && node.nodeValue.trim().toUpperCase() === t.key) {
                    card = node.parentElement.closest('.bg-white');
                    break;
                }
            }
        }

        if (card && !card.dataset.metricBound) {
            card.dataset.metricBound = "true";
            card.classList.add('cursor-pointer', 'transition-all', 'duration-150', 'hover:-translate-y-1', 'hover:shadow-md');
            card.addEventListener('click', (e) => {
                if (!e.target.closest('button') && !e.target.closest('a')) {
                    window.location.href = "{{ url('admin/analytics') }}/" + t.type;
                }
            });
        }
    });

    // 3. Real-Time Attendance Feed Live Search
    const searchInput = document.getElementById('feedSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.tap-row');

            rows.forEach(row => {
                const name = row.querySelector('.student-name')?.innerText.toLowerCase() || '';
                const id = row.querySelector('.student-id')?.innerText.toLowerCase() || '';
                const placement = row.querySelector('.student-placement')?.innerText.toLowerCase() || '';

                const matches = name.includes(query) || id.includes(query) || placement.includes(query);
                row.style.display = matches ? '' : 'none';
            });
        });
    }

    // 4. Modal Auto-Open kapag may validation errors sa Profile Form
    @if($errors->hasAny(['first_name', 'last_name', 'email', 'phone_number', 'current_password', 'password']))
        openEditProfileModal();
    @endif

    // 5. Global Escape Key Listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (typeof closeEditProfileModal === 'function') closeEditProfileModal();
            if (typeof closeSuccessModal === 'function') closeSuccessModal();
            if (typeof closeMetricModal === 'function') closeMetricModal();
        }
    });
}
</script>
@endpush