<?php
/**
 * WBACFSPWI — Mobile Prototype Dashboard
 * Optimized for smartphones and field demonstration matching Figure 4.
 */
require_once __DIR__ . '/../config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>WBACFSPWI — Mobile Controller</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <style>
        :root {
            --bg-phone: #090e17;
            --card-bg: #131b2a;
            --card-border: #1e293b;
            --accent-green: #10b981;
            --accent-amber: #f59e0b;
            --accent-red: #ef4444;
            --accent-cyan: #06b6d4;
            --accent-blue: #3b82f6;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-phone);
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .phone-shell {
            max-width: 440px;
            width: 100%;
            margin: 0 auto;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #0d1524 0%, #070b12 100%);
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.6);
        }

        /* Top Status Bar */
        .phone-header {
            padding: 1rem 1.25rem 0.75rem;
            border-bottom: 1px solid var(--card-border);
            background: rgba(19, 27, 42, 0.85);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .wifi-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .wifi-badge.offline {
            background: rgba(239, 68, 68, 0.15);
            color: var(--accent-red);
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* Hero Status Box - Matching Figure 4 Phone Display */
        .fig4-status-card {
            background: #111a2e;
            border: 2px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 1.5rem 1.25rem;
            margin: 1.25rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .fig4-status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #10b981);
        }

        .soil-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.35rem;
        }

        .soil-val-display {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.1;
            margin-bottom: 0.5rem;
        }

        .soil-val-display.dry {
            color: #ef4444;
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
        }

        .soil-val-display.optimal {
            color: #10b981;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }

        .soil-val-display.wet {
            color: #06b6d4;
            text-shadow: 0 0 20px rgba(6, 182, 212, 0.4);
        }

        /* Large Pump State Button Display */
        .pump-btn-display {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 80%;
            margin: 0.75rem auto 0;
            padding: 0.9rem 1.5rem;
            border-radius: 1rem;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pump-btn-display.pump-on {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.6);
            border: 2px solid #34d399;
            animation: pulse-pump 2s infinite;
        }

        .pump-btn-display.pump-off {
            background: #1e293b;
            color: #94a3b8;
            border: 2px solid #334155;
        }

        @keyframes pulse-pump {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 14px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            padding: 0 1.25rem 1.25rem;
        }

        .metric-box {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .metric-box .label {
            font-size: 0.75rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .metric-box .value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #f8fafc;
            font-family: 'JetBrains Mono', monospace;
        }

        .metric-box .sub {
            font-size: 0.7rem;
            color: #64748b;
        }

        /* Control Panel */
        .control-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            margin: 0 1.25rem 1.25rem;
            padding: 1.25rem;
        }

        .control-panel h6 {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 0.75rem;
        }

        .btn-override {
            font-weight: 700;
            border-radius: 0.75rem;
            padding: 0.65rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        /* Footer */
        .phone-footer {
            margin-top: auto;
            padding: 1rem;
            text-align: center;
            font-size: 0.75rem;
            color: #64748b;
            border-top: 1px solid var(--card-border);
        }
    </style>
</head>
<body>

<div class="phone-shell">
    <!-- Header with WiFi and Brand -->
    <div class="phone-header d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold text-white small d-block">WBACFSPWI PROTOTYPE</span>
            <span class="text-secondary" style="font-size: 0.7rem;">Figure 4 ESP32 Node</span>
        </div>
        <div class="wifi-badge" id="wifiStatusBadge">
            <i class="bi bi-wifi"></i>
            <span id="wifiStatusText">ESP32 Linked</span>
        </div>
    </div>

    <!-- Exact Figure 4 Phone Visual Card -->
    <div class="fig4-status-card">
        <div class="soil-label"><i class="bi bi-droplet-half me-1"></i> Soil Moisture Status</div>
        <div class="soil-val-display dry" id="soilStatusDisplay">Dry</div>
        <div class="text-secondary small mb-3">
            <span id="soilPctDisplay" class="fw-bold text-light">28.5%</span> (Threshold: Refill &lt; 40%)
        </div>

        <div class="soil-label mt-2"><i class="bi bi-power me-1"></i> Pump Actuator</div>
        <div class="pump-btn-display pump-on" id="pumpStatusDisplay">
            <i class="bi bi-water"></i>
            <span id="pumpStatusText">ON</span>
        </div>
        <div class="text-secondary mt-2" style="font-size: 0.75rem;" id="pumpSubText">
            Irrigating miniature field automatically
        </div>
    </div>

    <!-- Live Telemetry Metrics -->
    <div class="metrics-grid">
        <div class="metric-box">
            <div class="label"><i class="bi bi-battery-charging text-success"></i> 12V Battery</div>
            <div class="value" id="batteryVoltsDisplay">12.4 V</div>
            <div class="sub" id="batteryHealthDisplay">Normal (Good Charge)</div>
        </div>

        <div class="metric-box">
            <div class="label"><i class="bi bi-sun text-warning"></i> Solar Panel</div>
            <div class="value" id="solarVoltsDisplay">18.2 V</div>
            <div class="sub" id="solarStatusDisplay">Sunlight Active</div>
        </div>

        <div class="metric-box">
            <div class="label"><i class="bi bi-shield-check text-info"></i> Control Mode</div>
            <div class="value" style="font-size: 1.15rem;" id="modeDisplay">AUTO</div>
            <div class="sub">Autonomous Safety</div>
        </div>

        <div class="metric-box">
            <div class="label"><i class="bi bi-clock-history text-primary"></i> Last Packet</div>
            <div class="value" style="font-size: 1.15rem;" id="lastSeenDisplay">Just now</div>
            <div class="sub">1.5s stream</div>
        </div>
    </div>

    <!-- Manual Override Controls -->
    <div class="control-panel">
        <h6><i class="bi bi-sliders me-1"></i> Manual Pump Override</h6>
        <div class="row g-2">
            <div class="col-4">
                <button class="btn btn-success btn-override w-100" onclick="triggerOverride('on')">
                    <i class="bi bi-play-fill d-block fs-5"></i> Force ON
                </button>
            </div>
            <div class="col-4">
                <button class="btn btn-danger btn-override w-100" onclick="triggerOverride('off')">
                    <i class="bi bi-stop-fill d-block fs-5"></i> Force OFF
                </button>
            </div>
            <div class="col-4">
                <button class="btn btn-outline-info btn-override w-100" onclick="triggerOverride('auto')">
                    <i class="bi bi-arrow-repeat d-block fs-5"></i> Auto
                </button>
            </div>
        </div>
    </div>

    <!-- Links / Quick Navigation -->
    <div class="px-3 mb-3 d-flex gap-2">
        <a href="<?= BASE_URL ?>/prototype.php" class="btn btn-outline-primary btn-sm flex-fill">
            <i class="bi bi-diagram-3 me-1"></i> Interactive Simulator
        </a>
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm flex-fill">
            <i class="bi bi-speedometer2 me-1"></i> Admin Portal
        </a>
    </div>

    <!-- Footer -->
    <div class="phone-footer">
        WBACFSPWI Mobile Client &bull; Figure 4 Direct Prototype
    </div>
</div>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    let isFetching = false;

    async function pollTelemetry() {
        if (isFetching) return;
        isFetching = true;
        try {
            const res = await fetch(`${BASE_URL}/api/device/telemetry-public.php?t=${Date.now()}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            
            if (data.status === 'ok' && data.reading) {
                const r = data.reading;
                const soil = parseFloat(r.soil_moisture || 0);
                const pump = String(r.pump_state || 'off').toLowerCase();
                const vbat = parseFloat(r.battery_voltage || 12.0);
                const vsol = parseFloat(r.solar_output || 0);

                // Update Soil Display
                const soilEl = document.getElementById('soilStatusDisplay');
                const soilPctEl = document.getElementById('soilPctDisplay');
                soilPctEl.textContent = `${soil.toFixed(1)}%`;

                if (soil < 40.0) {
                    soilEl.textContent = 'Dry';
                    soilEl.className = 'soil-val-display dry';
                } else if (soil >= 65.0) {
                    soilEl.textContent = 'Wet';
                    soilEl.className = 'soil-val-display wet';
                } else {
                    soilEl.textContent = 'Optimal';
                    soilEl.className = 'soil-val-display optimal';
                }

                // Update Pump Display
                const pumpEl = document.getElementById('pumpStatusDisplay');
                const pumpText = document.getElementById('pumpStatusText');
                const pumpSub = document.getElementById('pumpSubText');

                if (pump === 'on') {
                    pumpEl.className = 'pump-btn-display pump-on';
                    pumpText.textContent = 'ON';
                    pumpSub.textContent = 'Pump running (irrigation active)';
                } else {
                    pumpEl.className = 'pump-btn-display pump-off';
                    pumpText.textContent = 'OFF';
                    pumpSub.textContent = 'Pump idle (target satisfied)';
                }

                // Voltages
                document.getElementById('batteryVoltsDisplay').textContent = `${vbat.toFixed(1)} V`;
                document.getElementById('solarVoltsDisplay').textContent = `${vsol.toFixed(1)} V`;

                // Solar status
                const solarStatusEl = document.getElementById('solarStatusDisplay');
                if (vsol >= 12.0) {
                    solarStatusEl.textContent = 'Charging Active';
                    solarStatusEl.className = 'sub text-success';
                } else if (vsol >= 5.0) {
                    solarStatusEl.textContent = 'Low Sun / Diffuse';
                    solarStatusEl.className = 'sub text-warning';
                } else {
                    solarStatusEl.textContent = 'Night / Disconnected';
                    solarStatusEl.className = 'sub text-muted';
                }

                // Mode
                if (data.override && data.override.command) {
                    document.getElementById('modeDisplay').textContent = `MANUAL (${data.override.command})`;
                } else {
                    document.getElementById('modeDisplay').textContent = 'AUTO';
                }

                document.getElementById('wifiStatusBadge').className = 'wifi-badge';
                document.getElementById('wifiStatusText').textContent = 'ESP32 Linked';
            }
        } catch (err) {
            console.warn('Telemetry fetch error:', err);
            document.getElementById('wifiStatusBadge').className = 'wifi-badge offline';
            document.getElementById('wifiStatusText').textContent = 'Reconnecting...';
        } finally {
            isFetching = false;
        }
    }

    async function triggerOverride(cmd) {
        try {
            const formData = new FormData();
            formData.append('action', cmd);
            const res = await fetch(`${BASE_URL}/api/admin/pump-control.php`, {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.status === 'ok') {
                pollTelemetry();
            } else {
                alert(result.message || 'Override rejected');
            }
        } catch (e) {
            console.error(e);
        }
    }

    setInterval(pollTelemetry, 1500);
    pollTelemetry();
</script>

</body>
</html>
