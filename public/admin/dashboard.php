<?php
require_once __DIR__ . '/partials/guard.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Soil Moisture</div>
                <div class="fs-3 fw-bold" id="stat-soil-moisture">--%</div>
                <div class="small text-warning-emphasis" id="soil-moisture-sample-note" style="display: none;">Temporary data — Arduino sensor not connected yet</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Water Level</div>
                <div class="fs-3 fw-bold" id="stat-water-level">--%</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Battery Level</div>
                <div class="fs-3 fw-bold" id="stat-battery">--%</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Solar Output</div>
                <div class="fs-3 fw-bold" id="stat-solar">-- W</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Pump State</div>
                <div class="fs-3 fw-bold">
                    <span class="badge bg-secondary" id="stat-pump-state">Unknown</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-muted small mb-1" id="stat-last-updated"></div>
<div class="mb-3" id="sample-data-notice" style="display: none;">
    <span class="badge bg-warning text-dark">Sample data</span>
    <span class="text-muted small">Arduino sensor is not connected yet — real readings will appear here automatically once it starts reporting.</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">Sensor Trends</div>
            <div class="card-body">
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Today's Schedule</div>
            <div class="card-body text-muted" id="today-schedules">
                Loading...
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Recent Alerts</div>
            <div class="card-body text-muted" id="recent-alerts">
                Loading...
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
let trendChart = null;

function renderTrendChart(trend) {
    const ctx = document.getElementById('trendChart');
    const config = {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [
                {
                    label: 'Soil Moisture (%)',
                    data: trend.soil_moisture,
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46,125,50,0.1)',
                    tension: 0.3,
                    spanGaps: true,
                },
                {
                    label: 'Water Level (%)',
                    data: trend.water_level,
                    borderColor: '#0288d1',
                    backgroundColor: 'rgba(2,136,209,0.1)',
                    tension: 0.3,
                    spanGaps: true,
                },
                {
                    label: 'Battery Level (%)',
                    data: trend.battery_percent,
                    borderColor: '#ef6c00',
                    backgroundColor: 'rgba(239,108,0,0.1)',
                    tension: 0.3,
                    spanGaps: true,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: { y: { min: 0, max: 100 }, x: { ticks: { maxTicksLimit: 8 } } },
        },
    };

    if (trendChart) {
        trendChart.data = config.data;
        trendChart.update();
    } else {
        trendChart = new Chart(ctx, config);
    }
}

function refreshDashboard() {
    fetch('<?= BASE_URL ?>/api/admin/dashboard-data.php')
        .then(res => res.json())
        .then(data => {
            const r = data.reading;
            document.getElementById('stat-soil-moisture').textContent = r && r.soil_moisture !== null ? r.soil_moisture + '%' : '--%';
            document.getElementById('stat-water-level').textContent = r && r.water_level !== null ? r.water_level + '%' : '--%';
            document.getElementById('stat-battery').textContent = r && r.battery_percent !== null ? r.battery_percent + '%' : '--%';
            document.getElementById('stat-solar').textContent = r && r.solar_output !== null ? r.solar_output + ' W' : '-- W';

            const pumpBadge = document.getElementById('stat-pump-state');
            if (r) {
                pumpBadge.textContent = r.pump_state === 'on' ? 'ON' : 'OFF';
                pumpBadge.className = 'badge ' + (r.pump_state === 'on' ? 'bg-success' : 'bg-secondary');
            }

            document.getElementById('stat-last-updated').textContent = r ? 'Last updated: ' + r.recorded_at : 'No sensor data received yet.';
            document.getElementById('sample-data-notice').style.display = data.is_sample ? '' : 'none';
            document.getElementById('soil-moisture-sample-note').style.display = data.is_sample ? '' : 'none';

            renderTrendChart(data.trend);

            const scheduleEl = document.getElementById('today-schedules');
            if (data.today_schedules.length === 0) {
                scheduleEl.textContent = 'No schedules run today.';
            } else {
                scheduleEl.innerHTML = '<ul class="list-unstyled mb-0">' + data.today_schedules.map(s =>
                    `<li class="mb-1"><strong>${s.label}</strong> — ${s.start_time} (${s.duration_minutes} min)</li>`
                ).join('') + '</ul>';
            }

            const alertsEl = document.getElementById('recent-alerts');
            if (data.alerts.length === 0) {
                alertsEl.textContent = 'No alerts yet.';
            } else {
                alertsEl.innerHTML = '<ul class="list-unstyled mb-0">' + data.alerts.map(a =>
                    `<li class="mb-1"><span class="badge bg-warning text-dark">${a.type}</span> ${a.message} <span class="text-muted small">(${a.created_at})</span></li>`
                ).join('') + '</ul>';
            }
        })
        .catch(() => {});
}

refreshDashboard();
setInterval(refreshDashboard, 15000);
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
