<?php
require_once __DIR__ . '/partials/guard.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Soil Moisture</div>
                <div class="fs-3 fw-bold" id="stat-soil-moisture">--%</div>
                <div class="small text-warning-emphasis" id="soil-moisture-sample-note" style="display: none;">Temporary data — Arduino sensor not connected yet</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Water Level</div>
                <div class="fs-3 fw-bold" id="stat-water-level">--%</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Battery Voltage</div>
                <div class="fs-3 fw-bold" style="color: #a855f7;" id="stat-battery">-- V</div>
                <div class="small text-muted" id="stat-battery-sub">Est. Charge: --%</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Solar Output</div>
                <div class="fs-3 fw-bold" id="stat-solar">-- W</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card stat-card shadow-sm border-0 h-100" style="border-left: 4px solid #3b82f6 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1 mb-2">
                    <div>
                        <div class="text-muted small">Pump Relay State</div>
                        <div class="fs-3 fw-bold">
                            <span class="badge bg-secondary" id="stat-pump-state">STANDBY</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Control Mode</div>
                        <span class="badge bg-dark border border-secondary text-info" id="stat-pump-mode">AUTOMATIC</span>
                    </div>
                </div>
                <div class="small text-muted mt-2 pt-2 border-top border-light-subtle" id="pump-status-sub">
                    Autonomous: ON &lt; 45%, OFF &ge; 50%
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
                <div class="chart-container-responsive position-relative">
                    <canvas id="trendChart"></canvas>
                    <div id="trendChartEmpty" class="position-absolute top-50 start-50 translate-middle text-muted text-center" style="display: none; pointer-events: none;">
                        <span class="fs-4 d-block mb-1">📡</span>
                        <span class="small">No sensor data received yet — waiting for Arduino / ESP8266 telemetry.</span>
                    </div>
                </div>
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
    const emptyMsg = document.getElementById('trendChartEmpty');
    const hasData = trend && trend.labels && trend.labels.length > 0;

    if (emptyMsg) {
        emptyMsg.style.display = hasData ? 'none' : 'block';
    }

    const config = {
        type: 'line',
        data: {
            labels: hasData ? trend.labels : [],
            datasets: [
                {
                    label: 'Soil Moisture (%)',
                    data: hasData ? trend.soil_moisture : [],
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46,125,50,0.1)',
                    tension: 0.3,
                    spanGaps: true,
                },
                {
                    label: 'Water Level (%)',
                    data: hasData ? trend.water_level : [],
                    borderColor: '#0288d1',
                    backgroundColor: 'rgba(2,136,209,0.1)',
                    tension: 0.3,
                    spanGaps: true,
                },
                {
                    label: 'Battery Voltage (V)',
                    data: hasData ? (trend.battery_voltage || trend.battery_percent) : [],
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168,85,247,0.1)',
                    tension: 0.3,
                    spanGaps: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
            
            if (r && r.battery_voltage !== null) {
                document.getElementById('stat-battery').textContent = Number(r.battery_voltage).toFixed(2) + ' V';
                if (document.getElementById('stat-battery-sub')) {
                    document.getElementById('stat-battery-sub').textContent = r.battery_percent !== null ? 'Est. Charge: ' + r.battery_percent + '%' : '';
                }
            } else {
                document.getElementById('stat-battery').textContent = '-- V';
                if (document.getElementById('stat-battery-sub')) {
                    document.getElementById('stat-battery-sub').textContent = 'Est. Charge: --%';
                }
            }

            document.getElementById('stat-solar').textContent = r && r.solar_output !== null ? r.solar_output + ' W' : '-- W';

            const pumpBadge = document.getElementById('stat-pump-state');
            if (r && r.pump_state) {
                pumpBadge.textContent = r.pump_state.toUpperCase();
                pumpBadge.className = 'badge ' + (r.pump_state === 'on' ? 'bg-success' : 'bg-secondary');
            } else {
                pumpBadge.textContent = 'STANDBY';
                pumpBadge.className = 'badge bg-secondary';
            }

            // Update Control Mode Indicator
            const modeBadge = document.getElementById('stat-pump-mode');
            const subEl = document.getElementById('pump-status-sub');
            if (data.active_command === 'PUMP_ON') {
                modeBadge.textContent = 'MANUAL ON';
                modeBadge.className = 'badge bg-success';
                if (subEl) subEl.textContent = 'Active Override: Pump forced ON';
            } else if (data.active_command === 'PUMP_OFF') {
                modeBadge.textContent = 'MANUAL OFF';
                modeBadge.className = 'badge bg-danger';
                if (subEl) subEl.textContent = 'Active Override: Pump forced OFF';
            } else {
                modeBadge.textContent = 'AUTOMATIC';
                modeBadge.className = 'badge bg-dark border border-secondary text-info';
                if (subEl) subEl.textContent = 'Autonomous: ON < 45%, OFF ≥ 50%';
            }

            document.getElementById('stat-last-updated').textContent = r ? 'Last updated: ' + r.recorded_at : 'No sensor data received yet — waiting for Arduino / ESP8266 to connect.';
            const sampleNotice = document.getElementById('sample-data-notice');
            if (sampleNotice) sampleNotice.style.display = 'none';
            const sampleNote = document.getElementById('soil-moisture-sample-note');
            if (sampleNote) sampleNote.style.display = 'none';

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
setInterval(refreshDashboard, 3000);
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
