<?php
require_once __DIR__ . '/../config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WBACFSPWI — Real-Time Live Telemetry Stream</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b1120;
            color: #f8fafc;
            min-height: 100vh;
            padding: 1.5rem 1rem;
        }
        .monitor-card {
            background-color: #111827;
            border: 1px solid #1f2937;
            border-radius: 1rem;
        }
        .metric-card {
            background-color: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 0.75rem;
            padding: 1.25rem;
            transition: all 0.2s ease;
        }
        .metric-card:hover { border-color: #38bdf8; }
        .mono-val { font-family: 'JetBrains Mono', monospace; }
        .live-dot {
            width: 14px; height: 14px; border-radius: 50%; display: inline-block;
        }
        .pulse-live { animation: pulse 1.5s infinite; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>
</head>
<body>
<div class="container-fluid" style="max-width: 1300px;">
    <!-- Top Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4 pb-3 border-bottom border-secondary">
        <div>
            <span class="badge bg-primary px-3 py-2 text-uppercase mb-2">Standalone Live Hardware Telemetry</span>
            <h2 class="h3 fw-bold mb-1">WBACFSPWI Rice Irrigation Controller Telemetry</h2>
            <p class="text-secondary small mb-0">Wireless ESP8266 WiFi &amp; Arduino Uno Direct Ingestion Stream</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline-info btn-sm">
                Go to Full Admin Dashboard &rarr;
            </a>
        </div>
    </div>

    <!-- Live Status Banner -->
    <div class="monitor-card p-3 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="live-dot" id="live-dot" style="background: #ef4444; flex-shrink: 0;"></span>
            <div>
                <span class="badge" id="live-badge" style="font-size: 0.95rem; background: #374151;">CONNECTING...</span>
                <span class="text-light ms-2 small d-block d-sm-inline" id="live-msg">Polling endpoint...</span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 gap-sm-4 text-secondary small">
            <div>Packets in Database: <strong class="text-info fs-5 mono-val" id="cnt-packets">0</strong></div>
            <div>Latest Packet Age: <strong class="text-light mono-val" id="age-packet">--</strong></div>
            <div>Battery Chem: <strong class="text-warning">12V Motorcycle Lead-Acid</strong></div>
        </div>
    </div>

    <!-- Live Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100" style="border-left: 4px solid #10b981;">
                <div class="text-secondary small fw-bold text-uppercase">🌱 Root Soil Moisture</div>
                <div class="fs-2 fs-sm-1 fw-bold text-success mono-val my-1" id="m-soil">--%</div>
                <div class="text-secondary small">Air: ~0% | Submerged: ~100%</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100" style="border-left: 4px solid #06b6d4;">
                <div class="text-secondary small fw-bold text-uppercase">🌊 Surface Water Level</div>
                <div class="fs-2 fs-sm-1 fw-bold text-info mono-val my-1" id="m-water">--%</div>
                <div class="text-secondary small">Refill: &lt;45% | Target Max: &ge;50%</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100" style="border-left: 4px solid #8b5cf6;">
                <div class="text-secondary small fw-bold text-uppercase">🔋 12V Battery Voltage</div>
                <div class="fs-2 fs-sm-1 fw-bold mono-val my-1" style="color: #c084fc;" id="m-battery">-- V</div>
                <div class="text-secondary small" id="m-battery-pct">Charge: --% (10.5V - 14.4V)</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="metric-card h-100" style="border-left: 4px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-secondary small fw-bold text-uppercase">☀️ Solar Panel Output</div>
                        <div class="fs-2 fs-sm-1 fw-bold text-warning mono-val my-1" id="m-solar">-- V</div>
                    </div>
                    <div class="text-end">
                        <div class="text-secondary small fw-bold text-uppercase">Pump State</div>
                        <span class="badge bg-secondary fs-6 mt-2" id="m-pump">STANDBY</span>
                    </div>
                </div>
                <div class="text-secondary small">Sunlight Harvesting: &gt;12.0V</div>
            </div>
        </div>
    </div>

    <!-- Live Stream Table & JSON Inspector -->
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="monitor-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                    <strong class="text-uppercase small">Hardware JSON Packet</strong>
                    <span class="badge bg-info mono-val" id="pkt-id">#--</span>
                </div>
                <pre class="text-success small mono-val mb-0" id="json-raw" style="background:#090d16; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; min-height: 220px; white-space: pre-wrap; word-break: break-all;">Waiting for telemetry packet...</pre>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="monitor-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary flex-wrap gap-2">
                    <strong class="text-uppercase small">Recent Telemetry Records (Last 20)</strong>
                    <span class="badge bg-dark border border-secondary text-secondary">Auto-Refresh 1s</span>
                </div>
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-dark table-striped table-hover text-center align-middle mb-0 small text-nowrap">
                        <thead class="table-secondary text-dark sticky-top">
                            <tr>
                                <th>#ID</th>
                                <th>Root Soil</th>
                                <th>Surface Ponding</th>
                                <th>12V Battery</th>
                                <th>Solar Volts</th>
                                <th>Pump Relay</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-body">
                            <tr><td colspan="7" class="text-muted py-3">No records in database.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const elDot = document.getElementById('live-dot');
    const elBadge = document.getElementById('live-badge');
    const elMsg = document.getElementById('live-msg');
    const elCnt = document.getElementById('cnt-packets');
    const elAge = document.getElementById('age-packet');

    const elSoil = document.getElementById('m-soil');
    const elWater = document.getElementById('m-water');
    const elBatt = document.getElementById('m-battery');
    const elBattPct = document.getElementById('m-battery-pct');
    const elSolar = document.getElementById('m-solar');
    const elPump = document.getElementById('m-pump');
    const elPktId = document.getElementById('pkt-id');
    const elJson = document.getElementById('json-raw');
    const elTbl = document.getElementById('tbl-body');

    function poll() {
        fetch('<?= BASE_URL ?>/api/device/telemetry-public.php')
            .then(r => r.json())
            .then(data => {
                elCnt.textContent = data.total_count;

                if (!data.latest) {
                    elDot.style.background = '#ef4444';
                    elDot.classList.remove('pulse-live');
                    elBadge.className = 'badge bg-danger';
                    elBadge.textContent = 'DATABASE EMPTY (0 ROWS)';
                    elMsg.textContent = 'Awaiting first packet from Arduino Uno / NodeMCU ESP8266.';
                    elAge.textContent = 'Never';
                    elSoil.textContent = '--%';
                    elWater.textContent = '--%';
                    elBatt.textContent = '-- V';
                    elSolar.textContent = '-- V';
                    elPump.textContent = 'STANDBY';
                    elPump.className = 'badge bg-secondary fs-6 mt-2';
                    elJson.textContent = 'Waiting for transmission...';
                    elTbl.innerHTML = '<tr><td colspan="7" class="text-muted py-3">No data in database. Power on Arduino Uno or trigger a reading.</td></tr>';
                    return;
                }

                const r = data.latest;
                elPktId.textContent = `#${r.id}`;

                if (data.is_live) {
                    elDot.style.background = '#10b981';
                    elDot.classList.add('pulse-live');
                    elBadge.className = 'badge bg-success';
                    elBadge.textContent = '🟢 LIVE HARDWARE TRANSMITTING';
                    elMsg.textContent = `Streaming every 1 second (last: ${data.seconds_ago}s ago)`;
                } else {
                    elDot.style.background = '#f59e0b';
                    elDot.classList.remove('pulse-live');
                    elBadge.className = 'badge bg-warning text-dark';
                    elBadge.textContent = '🟡 IDLE / NO RECENT DATA';
                    elMsg.textContent = `Last packet arrived ${data.seconds_ago}s ago`;
                }

                elAge.textContent = `${data.seconds_ago}s ago`;
                elSoil.textContent = `${r.soil_moisture}%`;
                elWater.textContent = `${r.water_level}%`;
                elBatt.textContent = `${r.battery_voltage.toFixed(2)} V`;
                elBattPct.textContent = `Charge: ${r.battery_percent !== null ? r.battery_percent + '%' : 'N/A'}`;
                elSolar.textContent = `${r.solar_output.toFixed(2)} V`;

                if (r.pump_state === 'on') {
                    elPump.textContent = 'IRRIGATING';
                    elPump.className = 'badge bg-success fs-6 mt-2';
                } else {
                    elPump.textContent = 'STANDBY';
                    elPump.className = 'badge bg-secondary fs-6 mt-2';
                }

                elJson.textContent = JSON.stringify(r, null, 2);

                let rows = '';
                data.recent.forEach(row => {
                    const badge = row.pump_state === 'on' 
                        ? '<span class="badge bg-success">ON</span>' 
                        : '<span class="badge bg-secondary">OFF</span>';
                    rows += `
                        <tr>
                            <td><span class="badge bg-dark border">#${row.id}</span></td>
                            <td class="text-success fw-bold">${row.soil_moisture.toFixed(1)}%</td>
                            <td class="text-info fw-bold">${row.water_level.toFixed(1)}%</td>
                            <td style="color:#c084fc;">${row.battery_voltage.toFixed(2)} V</td>
                            <td class="text-warning">${row.solar_output.toFixed(2)} V</td>
                            <td>${badge}</td>
                            <td class="text-secondary small">${row.recorded_at}</td>
                        </tr>
                    `;
                });
                elTbl.innerHTML = rows;
            })
            .catch(err => {
                console.error(err);
            });
    }

    setInterval(poll, 1000);
    poll();
});
</script>
</body>
</html>
