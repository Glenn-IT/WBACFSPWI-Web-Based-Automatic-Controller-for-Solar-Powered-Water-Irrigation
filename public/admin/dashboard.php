<?php
require_once __DIR__ . '/partials/guard.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Dashboard</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Soil Moisture</div>
                <div class="fs-3 fw-bold" id="stat-soil-moisture">--%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Battery Voltage</div>
                <div class="fs-3 fw-bold" id="stat-battery">-- V</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Solar Output</div>
                <div class="fs-3 fw-bold" id="stat-solar">-- W</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
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

<div class="text-muted small mb-3" id="stat-last-updated"></div>

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

<script>
function refreshDashboard() {
    fetch('/api/admin/dashboard-data.php')
        .then(res => res.json())
        .then(data => {
            const r = data.reading;
            document.getElementById('stat-soil-moisture').textContent = r && r.soil_moisture !== null ? r.soil_moisture + '%' : '--%';
            document.getElementById('stat-battery').textContent = r && r.battery_voltage !== null ? r.battery_voltage + ' V' : '-- V';
            document.getElementById('stat-solar').textContent = r && r.solar_output !== null ? r.solar_output + ' W' : '-- W';

            const pumpBadge = document.getElementById('stat-pump-state');
            if (r) {
                pumpBadge.textContent = r.pump_state === 'on' ? 'ON' : 'OFF';
                pumpBadge.className = 'badge ' + (r.pump_state === 'on' ? 'bg-success' : 'bg-secondary');
            }

            document.getElementById('stat-last-updated').textContent = r ? 'Last updated: ' + r.recorded_at : 'No sensor data received yet.';

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
