<?php
require_once __DIR__ . '/partials/guard.php';

$pageTitle = 'Live Telemetry Monitor & Integration Test';
$activePage = 'telemetry';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">📡 Live Telemetry Monitor & Integration Test</h4>
        <p class="text-muted small mb-0">Direct real-time hardware packet inspector between Arduino Uno, NodeMCU ESP8266, and MySQL database.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-primary" id="btn-inject-packet">
            ⚡ Inject Test Packet
        </button>
        <button class="btn btn-sm btn-outline-danger" id="btn-purge-data">
            🗑️ Purge to 0 Data
        </button>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-sm btn-success" target="_blank">
            📊 Open Main Dashboard ↗
        </a>
    </div>
</div>

<!-- Real-Time Hardware Link Status Bar -->
<div class="card bg-dark border-secondary shadow-sm mb-4">
    <div class="card-body py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div id="status-indicator-dot" style="width: 16px; height: 16px; border-radius: 50%; background: #6c757d;"></div>
            <div>
                <span class="badge" id="status-badge" style="font-size: 0.95rem; background: #343a40;">CHECKING DATABASE...</span>
                <span class="text-light ms-2 small" id="status-message">Initializing live telemetry poll...</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4 text-secondary small">
            <div>Total Database Records: <strong class="text-info fs-5" id="val-total-records">0</strong></div>
            <div>Last Packet Arrival: <strong class="text-light" id="val-last-arrival">--</strong></div>
            <div>Active Battery Profile: <strong class="text-warning">12V Motorcycle Lead-Acid (10.5V - 14.4V)</strong></div>
        </div>
    </div>
</div>

<!-- Live Metric Cards (Latest Packet) -->
<div class="row g-3 mb-4">
    <!-- Root Soil Moisture -->
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0" style="border-left: 4px solid #10b981 !important;">
            <div class="card-body">
                <div class="text-muted small fw-bold text-uppercase">🌱 Root Soil Moisture</div>
                <div class="fs-2 fw-bold text-success" id="card-soil">--%</div>
                <div class="small text-muted">Capacitive (A0) • Air 0% / Water 100%</div>
            </div>
        </div>
    </div>

    <!-- Surface Ponding Water Level -->
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0" style="border-left: 4px solid #06b6d4 !important;">
            <div class="card-body">
                <div class="text-muted small fw-bold text-uppercase">🌊 Surface Water Level</div>
                <div class="fs-2 fw-bold text-info" id="card-water">--%</div>
                <div class="small text-muted" id="card-water-sub">Refill: &lt;45% | Target: &ge;50%</div>
            </div>
        </div>
    </div>

    <!-- 12V Battery Voltage -->
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0" style="border-left: 4px solid #8b5cf6 !important;">
            <div class="card-body">
                <div class="text-muted small fw-bold text-uppercase">🔋 12V Battery Voltage</div>
                <div class="fs-2 fw-bold" style="color: #a855f7;" id="card-battery">-- V</div>
                <div class="small text-muted" id="card-battery-sub">Calculated Charge: --%</div>
            </div>
        </div>
    </div>

    <!-- Solar Voltage & Pump State -->
    <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0" style="border-left: 4px solid #f59e0b !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">☀️ Solar Panel Output</div>
                        <div class="fs-2 fw-bold text-warning" id="card-solar">-- V</div>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase text-end">Pump Relay</div>
                        <div class="mt-1 text-end">
                            <span class="badge bg-secondary fs-6" id="card-pump">OFF</span>
                        </div>
                    </div>
                </div>
                <div class="small text-muted mt-1" id="card-solar-sub">Harvesting: &gt;12.0V</div>
            </div>
        </div>
    </div>
</div>

<!-- Raw Payload & Live Ingestion Stream -->
<div class="row g-3">
    <!-- Left Column: Raw JSON Packet from Last Hardware Stream -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-uppercase">📦 Latest Hardware JSON Packet</span>
                <span class="badge bg-info" id="badge-latest-id">ID: None</span>
            </div>
            <div class="card-body bg-dark text-light p-3">
                <pre class="mb-0 text-success" id="raw-json-payload" style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; white-space: pre-wrap; word-break: break-all;">Waiting for first transmission from Arduino Uno / NodeMCU ESP8266...</pre>
            </div>
            <div class="card-footer bg-dark border-secondary small text-muted">
                Endpoint: <code>POST /api/device/report.php</code><br>
                Header: <code>X-API-Key: dev-local-device-key</code>
            </div>
        </div>
    </div>

    <!-- Right Column: Database Feed Table (Last 25 Rows) -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-uppercase">🗄️ Database Record Stream (Last 25 Received)</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="chk-auto-refresh" checked>
                    <label class="form-check-label text-light small" for="chk-auto-refresh">Live Polling (1.5s)</label>
                </div>
            </div>
            <div class="card-body p-0 table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-dark table-hover table-striped mb-0 text-center align-middle" style="font-size: 0.88rem;">
                    <thead class="table-secondary text-dark sticky-top">
                        <tr>
                            <th>#ID</th>
                            <th>Root Soil</th>
                            <th>Surface Water</th>
                            <th>12V Battery</th>
                            <th>Solar Volts</th>
                            <th>Pump Relay</th>
                            <th>Server Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="telemetry-table-body">
                        <tr>
                            <td colspan="7" class="text-muted py-4">Database is empty (0 records). Power on Arduino or click "Inject Test Packet" above.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastSeenId = 0;
    let pollTimer = null;

    const elIndicatorDot = document.getElementById('status-indicator-dot');
    const elBadge = document.getElementById('status-badge');
    const elMessage = document.getElementById('status-message');
    const elTotalRecords = document.getElementById('val-total-records');
    const elLastArrival = document.getElementById('val-last-arrival');

    const elSoil = document.getElementById('card-soil');
    const elWater = document.getElementById('card-water');
    const elBattery = document.getElementById('card-battery');
    const elBatterySub = document.getElementById('card-battery-sub');
    const elSolar = document.getElementById('card-solar');
    const elPump = document.getElementById('card-pump');

    const elRawJson = document.getElementById('raw-json-payload');
    const elBadgeLatestId = document.getElementById('badge-latest-id');
    const elTableBody = document.getElementById('telemetry-table-body');
    const elAutoRefresh = document.getElementById('chk-auto-refresh');

    function fetchTelemetry() {
        fetch('<?= BASE_URL ?>/api/admin/telemetry-live.php')
            .then(res => res.json())
            .then(data => {
                updateUI(data);
            })
            .catch(err => {
                console.error('Polling error:', err);
                elBadge.textContent = 'API ERROR';
                elBadge.className = 'badge bg-danger';
                elMessage.textContent = 'Could not reach server endpoint.';
            });
    }

    function updateUI(data) {
        elTotalRecords.textContent = data.total_count;

        if (!data.latest) {
            // 0 Data state
            elIndicatorDot.style.background = '#ef4444';
            elIndicatorDot.style.boxShadow = 'none';
            elBadge.className = 'badge bg-danger';
            elBadge.textContent = 'DATABASE EMPTY (0 ROWS)';
            elMessage.textContent = 'Waiting for first telemetry transmission from Arduino Uno & NodeMCU...';
            elLastArrival.textContent = 'Never';

            elSoil.textContent = '--%';
            elWater.textContent = '--%';
            elBattery.textContent = '-- V';
            elBatterySub.textContent = 'Calculated Charge: --%';
            elSolar.textContent = '-- V';
            elPump.textContent = 'OFF';
            elPump.className = 'badge bg-secondary fs-6';

            elRawJson.textContent = '{\n  "status": "waiting_for_hardware",\n  "total_records": 0\n}';
            elBadgeLatestId.textContent = 'ID: None';

            elTableBody.innerHTML = '<tr><td colspan="7" class="text-muted py-4">Database is empty (0 records). Power on Arduino Uno or click "Inject Test Packet".</td></tr>';
            return;
        }

        // We have data!
        const latest = data.latest;
        elBadgeLatestId.textContent = `ID: #${latest.id}`;

        if (data.is_live) {
            elIndicatorDot.style.background = '#10b981';
            elIndicatorDot.style.boxShadow = '0 0 10px #10b981';
            elBadge.className = 'badge bg-success';
            elBadge.textContent = '🟢 HARDWARE STREAMING LIVE';
            elMessage.textContent = `Packet #${latest.id} received ${data.seconds_ago}s ago`;
        } else {
            elIndicatorDot.style.background = '#f59e0b';
            elIndicatorDot.style.boxShadow = 'none';
            elBadge.className = 'badge bg-warning text-dark';
            elBadge.textContent = '🟡 STANDBY / NO RECENT PACKET';
            elMessage.textContent = `Last packet #${latest.id} was received ${data.seconds_ago}s ago`;
        }

        elLastArrival.textContent = `${latest.recorded_at} (${data.seconds_ago}s ago)`;

        // Cards
        elSoil.textContent = `${latest.soil_moisture}%`;
        elWater.textContent = `${latest.water_level}%`;
        elBattery.textContent = `${latest.battery_voltage.toFixed(2)} V`;
        elBatterySub.textContent = `Calculated Charge: ${latest.battery_percent !== null ? latest.battery_percent + '%' : 'N/A'}`;
        elSolar.textContent = `${latest.solar_output.toFixed(2)} V`;

        if (latest.pump_state === 'on') {
            elPump.textContent = 'ON (PUMPING)';
            elPump.className = 'badge bg-success fs-6';
        } else {
            elPump.textContent = 'OFF (STANDBY)';
            elPump.className = 'badge bg-secondary fs-6';
        }

        // Raw JSON Display
        elRawJson.textContent = JSON.stringify({
            id: latest.id,
            soil_moisture: latest.soil_moisture,
            water_level: latest.water_level,
            battery_voltage: latest.battery_voltage,
            battery_percent: latest.battery_percent,
            solar_output: latest.solar_output,
            pump_state: latest.pump_state,
            recorded_at: latest.recorded_at,
            seconds_ago: data.seconds_ago
        }, null, 2);

        // Table Rows
        let html = '';
        data.recent.forEach(r => {
            const isNew = (r.id > lastSeenId && lastSeenId !== 0);
            const pumpBadge = r.pump_state === 'on' 
                ? '<span class="badge bg-success">ON</span>' 
                : '<span class="badge bg-secondary">OFF</span>';

            html += `
                <tr ${isNew ? 'class="table-primary fw-bold"' : ''}>
                    <td><span class="badge bg-dark border">#${r.id}</span></td>
                    <td class="text-success fw-bold">${r.soil_moisture.toFixed(1)}%</td>
                    <td class="text-info fw-bold">${r.water_level.toFixed(1)}%</td>
                    <td style="color: #c084fc;">${r.battery_voltage.toFixed(2)} V</td>
                    <td class="text-warning">${r.solar_output.toFixed(2)} V</td>
                    <td>${pumpBadge}</td>
                    <td class="text-muted small">${r.recorded_at}</td>
                </tr>
            `;
        });
        elTableBody.innerHTML = html;
        lastSeenId = latest.id;
    }

    // Controls
    document.getElementById('btn-inject-packet').addEventListener('click', function() {
        fetch('<?= BASE_URL ?>/api/admin/telemetry-live.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=inject'
        })
        .then(res => res.json())
        .then(res => {
            fetchTelemetry();
        });
    });

    document.getElementById('btn-purge-data').addEventListener('click', function() {
        if (!confirm('Are you sure you want to reset all sensor readings back to 0 rows?')) return;
        fetch('<?= BASE_URL ?>/api/admin/telemetry-live.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=clear'
        })
        .then(res => res.json())
        .then(res => {
            lastSeenId = 0;
            fetchTelemetry();
        });
    });

    // Auto-polling interval (1.5 seconds)
    pollTimer = setInterval(function() {
        if (elAutoRefresh.checked) {
            fetchTelemetry();
        }
    }, 1500);

    // Initial load
    fetchTelemetry();
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
