@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')

<style>
/* PAGE */
html, body {
    background: #f8fafc;
    height: 100%;
    overflow-y: auto !important;
    font-family: 'Inter', sans-serif;
}

/* CONTAINER */
.dashboard-container {
    background-color: #f8fafc;
    max-width: 1400px;
    margin: auto;
    padding: 20px;
}

/* GRID */
.grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

@media (max-width: 992px) {
    .grid-3 {
        grid-template-columns: 1fr;
    }
}

/* CARD */
.card {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    min-height: 320px;
    position: relative;
}

.card h6 {
    font-weight: 600;
    font-size: 14px;
    color: #2c2c2c;
    margin-bottom: 12px;
}

/* SPACING */
.mt-20 { margin-top: 20px; }

/* CANVAS FIX */
canvas {
    width: 100% !important;
    height: 240px !important;
}

/* ALERTS */
.alert {
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 13px;
    font-weight: 500;
}
.alert.danger { background:#fdecea; color:#d32f2f; }
.alert.warning { background:#fff4e5; color:#ef6c00; }
.alert.info { background:#e3f2fd; color:#1976d2; }

/* CARD HEADER FIX */
.card-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.card-header-custom h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

/* FILTER GROUP */
.header-filters {
    display: flex;
    gap: 8px;
}

.header-filters select {
    min-width: 110px;
}


/* TABLE */
.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.table th {
    background: #f1f3f6;
    padding: 10px;
    text-align: left;
    font-weight: 600;
}

.table td {
    padding: 10px;
    border-bottom: 1px solid #e6e6e6;
}

/* BADGES */
.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    color: #fff;
}
.badge.green { background:#43a047; }
.badge.red { background:#e53935; }
.badge.orange { background:#fb8c00; }

.green { color:#43a047; font-weight:600; }
.red { color:#e53935; font-weight:600; }
.orange { color:#fb8c00; font-weight:600; }

/* TEAM */
.team-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.team-list li {
    padding: 12px 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 500;
}

.team-list .good { background:#e8f5e9; }
.team-list .neutral { background:#f5f5f5; }
.team-list .bad { background:#fdecea; }

/* ================= HEATMAP COLORS ================= */
.hm-good {
    background-color: #e8f5e9 !important;
    color: #2e7d32 !important;
    font-weight: 600;
}

.hm-warning {
    background-color: #fff8e1 !important;
    color: #ef6c00 !important;
    font-weight: 600;
}

.hm-bad {
    background-color: #fdecea !important;
    color: #c62828 !important;
    font-weight: 600;
}

/* Table polish */
.heatmap-table td {
    vertical-align: middle;
    transition: background-color 0.3s ease;
}

.heatmap-table tr:hover td {
    background-color: #f9fafb;
}

</style>

<div class="dashboard-container">

    <!-- ROW 1 -->
    <div class="grid-3">
        <div class="card">

        <!-- HEADER -->
<div class="card-header-custom">
    <h6 class="m-0">Velocity Trend</h6>

    <div class="header-filters">
        <select id="velocityRange" class="form-select form-select-sm">
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="7days" selected>Last 7 Days</option>
        </select>

       <select id="velocityGType" class="mini-select">
                <option value="" selected>Generated</option>
                <option value="all">All</option>
                <option value="auto">Auto</option>
                <option value="manual">Manual</option>
            </select>


        <select id="velocityType" class="form-select form-select-sm">
            <option value="" selected>Completed</option>
            <option value="all">All</option>
            <option value="auto">Auto</option>
            <option value="manual">Manual</option>
        </select>
    </div>
</div>

        <!-- CHART -->
        <canvas id="velocityChart"></canvas>

    </div>

        <div class="card">
            <h6>Backlog Burndown & Forecast</h6>
            <canvas id="burndownChart"></canvas>
        </div>

        <div class="card alert-card">
            <h6>? Alerts & Exceptions</h6>
            <div class="alert danger">SLA Breach Spike in Krishna – 15 tickets</div>
            <div class="alert warning">Zero Progress in Block “Kankipadu”</div>
            <div class="alert warning">Ticket Stuck &gt; 5 Days</div>
            <div class="alert info">Missing Photos / GPS</div>
        </div>
    </div>

    <!-- ROW 2 -->
<div class="card mt-20">

    <!-- HEADER -->
    <div class="card-header-custom">
        <h6 class="m-0">District Performance Heatmap</h6>

        <div class="header-filters">
            <select id="districtRange" class="mini-select">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="7days" selected>Last 7 Days</option>
            </select>
            
            <select id="districtGType" class="mini-select">
                <option value="" selected>Generated</option>
                <option value="all">All</option>
                <option value="auto">Auto</option>
                <option value="manual">Manual</option>
            </select>


            <select id="districtType" class="mini-select">
                <option value="" selected>Completed</option>
                <option value="all">All</option>
                <option value="auto">Auto</option>
                <option value="manual">Manual</option>
            </select>
        </div>
    </div>

    <!-- TABLE -->
    <table class="table heatmap-table">
        <thead>
    <tr>
        <th>District</th>
        <th>Assigned</th>
        <th>Closed</th>
        <th>Open</th>
        <th>SLA (Reached / Missed)</th>
        <th>SLA %</th>
        <th>Net Velocity</th>
    </tr>
</thead>
        <tbody id="districtHeatmapBody">
            <tr>
                <td colspan="6" class="text-center text-muted">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>

    <!-- ROW 3 -->
    <div class="grid-3 mt-20">
        <div class="card">
            <h6>Ticket Aging Distribution</h6>
            <canvas id="agingChart"></canvas>
        </div>

        <div class="card">
            <h6>Root Cause Analysis</h6>
            <canvas id="rootCauseChart"></canvas>
        </div>

        <div class="card">
            <h6>Team Productivity</h6>
            <ul class="team-list">
                <li class="good">Ravi Kumar <span>15 Closed</span></li>
                <li class="good">Priya Sharma <span>12 Closed</span></li>
                <li class="neutral">Anil Reddy <span>8 Closed</span></li>
                <li class="bad">Suresh Gupta <span>2 Closed</span></li>
            </ul>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let velocityChart;
let backlogChart;

/* ================= VELOCITY CHART ================= */
function loadVelocity() {

    const range = document.getElementById('velocityRange').value;
    const type  = document.getElementById('velocityType').value;
    const g_type  = document.getElementById('velocityGType').value;

    fetch(`/admin/velocity?range=${range}&type=${type}&g_type=${g_type}`)
        .then(res => res.json())
        .then(data => {
            renderVelocityChart(data);
            renderBacklogChart(data.labels, data.not_started);
        });
}

function renderVelocityChart(data) {

    const ctx = document.getElementById('velocityChart').getContext('2d');
    if (velocityChart) velocityChart.destroy();

    velocityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Assigned',
                    data: data.assigned,
                    borderColor: '#1e88e5',
                    backgroundColor: 'rgba(30,136,229,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.4
                },
                {
                    label: 'Closed',
                    data: data.closed,
                    borderColor: '#43a047',
                    backgroundColor: 'rgba(67,160,71,0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.4
                },
                {
                    label: 'Hold',
                    data: data.hold,
                    borderColor: '#fb8c00',
                    borderDash: [6,6],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.4
                },
                {
                    label: 'Not Started',
                    data: data.not_started,
                    borderColor: '#e53935',
                    borderDash: [4,4],
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    tension: 0.4
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },

                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 10,
                    cornerRadius: 6,
                    callbacks: {
                        title: function (items) {
                            return 'Time : ' + items[0].label;
                        },
                        label: function (item) {
                            return item.dataset.label + ' : ' + item.formattedValue;
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

/* ================= BACKLOG CHART ================= */
function renderBacklogChart(labels, notStarted) {

    const ctx = document.getElementById('burndownChart').getContext('2d');
    if (backlogChart) backlogChart.destroy();

    backlogChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Actual Backlog (Not Started)',
                    data: notStarted,
                    borderColor: '#e53935',
                    backgroundColor: 'rgba(229,57,53,0.15)',
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            animation: {
                duration: 1000,
                easing: 'easeOutCubic'
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },

            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
}

/* ================= INIT ================= */
loadVelocity();

/* ================= FILTER EVENTS ================= */
document.getElementById('velocityRange').addEventListener('change', loadVelocity);
document.getElementById('velocityType').addEventListener('change', loadVelocity);
document.getElementById('velocityGType').addEventListener('change', loadVelocity);

</script>
<script>
new Chart(document.getElementById('agingChart'), {
    type: 'bar',
    data: {
        labels: ['0–3 Days', '4–7 Days', '8–15 Days', '>15 Days'],
        datasets: [{
            label: 'Tickets',
            data: [120, 95, 60, 25],
            backgroundColor: [
                '#43a047',
                '#fbc02d',
                '#fb8c00',
                '#e53935'
            ]
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { beginAtZero: true }
        }
    }
});
</script>
<script>
new Chart(document.getElementById('rootCauseChart'), {
    type: 'bar',
    data: {
        labels: [
            'ROW Issues',
            'Material Waiting',
            'Team Shortage',
            'Route Change',
            'BSNL Pending'
        ],
        datasets: [{
            label: 'Tickets',
            data: [45, 32, 26, 18, 10],
            backgroundColor: '#1e88e5'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

<script>
function loadDistrictHeatmap() {

    const range = document.getElementById('districtRange').value;
    const type  = document.getElementById('districtType').value;
    const g_type  = document.getElementById('districtGType').value;

    fetch(`/admin/district-heatmap?range=${range}&type=${type}&g_type=${g_type}`)
        .then(res => res.json())
        .then(rows => {

            let html = '';

            if (!rows.length) {
                html = `<tr><td colspan="7" class="text-center text-muted">No data</td></tr>`;
            }

            rows.forEach(r => {

                /* Backlog Heat */
                let backlogClass =
                    r.backlog > 100 ? 'hm-bad' :
                    r.backlog > 50  ? 'hm-warning' :
                                      'hm-good';

                /* SLA % Heat */
                let slaClass =
                    r.sla_percent < 90 ? 'hm-bad' :
                    r.sla_percent < 95 ? 'hm-warning' :
                                          'hm-good';

                /* Velocity Heat */
                let velocityClass =
                    r.net_velocity < 0 ? 'hm-bad' : 'hm-good';

                html += `
                    <tr>
                        <td><strong>${r.district}</strong></td>

                        <td>${r.assigned}</td>

                        <td>${r.closed}</td>

                        <td class="${backlogClass}">
                            ${r.backlog}
                        </td>

                        <td>
                            <span class="text-success fw-semibold">
                                ${r.sla_pass}
                            </span>
                            /
                            <span class="text-danger fw-semibold">
                                ${r.sla_fail}
                            </span>
                        </td>

                        <td class="${slaClass}">
                            ${r.sla_percent}%
                        </td>

                        <td class="${velocityClass}">
                            ${r.net_velocity > 0 ? '+' : ''}${r.net_velocity}
                        </td>
                    </tr>
                `;
            });

            document.getElementById('districtHeatmapBody').innerHTML = html;
        });
}

/* INIT */
loadDistrictHeatmap();

/* FILTER EVENTS */
document.getElementById('districtRange').addEventListener('change', loadDistrictHeatmap);
document.getElementById('districtType').addEventListener('change', loadDistrictHeatmap);
document.getElementById('districtGType').addEventListener('change', loadDistrictHeatmap);

</script>


@endsection



