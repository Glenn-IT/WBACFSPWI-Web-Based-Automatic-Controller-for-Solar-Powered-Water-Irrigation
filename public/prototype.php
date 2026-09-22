<?php
/**
 * WBACFSPWI — Interactive Figure 4 Prototype Simulator
 * High-fidelity visual circuit simulator & live telemetry bridge matching Figure 4.
 */
require_once __DIR__ . '/../config/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WBACFSPWI — Figure 4 Interactive Prototype Simulator</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <style>
        :root {
            --bg-page: #080d1a;
            --bg-panel: #0f172a;
            --bg-card: #1e293b;
            --border-panel: #334155;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --accent-green: #10b981;
            --accent-blue: #38bdf8;
            --accent-amber: #f59e0b;
            --accent-red: #ef4444;
            --accent-purple: #a855f7;
            --wire-pos: #ef4444;
            --wire-neg: #111827;
            --wire-sig: #10b981;
            --wire-relay: #38bdf8;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg-page);
            color: var(--text-light);
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            padding: 1.5rem;
            overflow-x: hidden;
        }

        .mono { font-family: 'JetBrains Mono', monospace; }

        .simulator-canvas-wrapper {
            background: #090e1a;
            border: 2px solid var(--border-panel);
            border-radius: 1.25rem;
            position: relative;
            min-height: 680px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
            background-image: 
                radial-gradient(circle at 50% 50%, rgba(30, 41, 59, 0.4) 0%, transparent 80%),
                linear-gradient(to right, rgba(56, 189, 248, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(56, 189, 248, 0.04) 1px, transparent 1px);
            background-size: 100% 100%, 30px 30px, 30px 30px;
        }

        /* Hardware Component Modules on Canvas */
        .hardware-module {
            position: absolute;
            background: #131c31;
            border: 2px solid #334155;
            border-radius: 12px;
            padding: 0.85rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            transition: all 0.25s ease;
            z-index: 10;
        }

        .hardware-module:hover {
            border-color: var(--accent-blue);
            transform: translateY(-2px);
        }

        .module-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* 1. Solar Panel */
        #mod-solar {
            top: 25px;
            left: 30px;
            width: 220px;
            border-color: #ca8a04;
        }

        .solar-grid {
            height: 90px;
            background: linear-gradient(135deg, #1e3a8a 0%, #0c4a6e 100%);
            border: 2px solid #64748b;
            border-radius: 6px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 2px;
            padding: 2px;
            position: relative;
            overflow: hidden;
        }

        .solar-cell {
            background: rgba(56, 189, 248, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .solar-sun-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.3) 0%, transparent 70%);
            pointer-events: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        /* 2. Solar Charge Controller */
        #mod-controller {
            top: 25px;
            left: 300px;
            width: 280px;
            border-color: #2563eb;
            background: #0f1e3d;
        }

        .controller-lcd {
            background: #032b2b;
            border: 2px solid #088395;
            border-radius: 6px;
            padding: 8px 12px;
            color: #00ffcc;
            font-family: 'JetBrains Mono', monospace;
            text-shadow: 0 0 8px rgba(0,255,204,0.6);
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 0.75rem;
        }

        .controller-terminals {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
            border-top: 1px solid #1e3a8a;
            padding-top: 6px;
        }

        .term-pair {
            display: flex;
            gap: 4px;
            font-size: 0.65rem;
            color: #94a3b8;
            text-align: center;
        }

        .screw-head {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #94a3b8;
            border: 1px solid #334155;
            display: inline-block;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.6);
        }

        /* 3. 12V Battery */
        #mod-battery {
            top: 220px;
            left: 30px;
            width: 220px;
            border-color: #475569;
            background: #182234;
        }

        .battery-body {
            height: 85px;
            background: #0a0f1d;
            border-radius: 6px;
            border: 2px solid #334155;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .battery-terminals-top {
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin-bottom: 6px;
        }

        .bat-post {
            width: 18px;
            height: 12px;
            border-radius: 3px 3px 0 0;
            font-weight: 800;
            font-size: 0.65rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bat-post.pos { background: #ef4444; }
        .bat-post.neg { background: #374151; }

        /* 4. Solderless Breadboard */
        #mod-breadboard {
            top: 220px;
            left: 300px;
            width: 440px;
            border-color: #64748b;
            background: #f1f5f9;
            color: #0f172a;
        }

        .bb-surface {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px;
            position: relative;
        }

        .bb-power-rails {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #ef4444;
            padding-bottom: 3px;
            margin-bottom: 5px;
            font-size: 0.65rem;
            font-weight: 700;
        }

        .bb-power-rails.gnd-rail {
            border-bottom: 2px solid #0284c7;
            margin-bottom: 10px;
        }

        /* 5. ESP32 Microcontroller */
        #mod-esp32 {
            top: 380px;
            left: 30px;
            width: 230px;
            border-color: #3b82f6;
            background: #0d172c;
        }

        .esp-chip {
            background: #1e293b;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .esp-metal-can {
            width: 65px;
            height: 60px;
            background: linear-gradient(135deg, #94a3b8 0%, #cbd5e1 50%, #64748b 100%);
            border-radius: 4px;
            border: 1px solid #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 800;
            color: #0f172a;
            box-shadow: 0 2px 5px rgba(0,0,0,0.4);
        }

        .esp-led {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .led-blue { background: #38bdf8; box-shadow: 0 0 8px #38bdf8; }
        .led-red { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

        /* 6. Soil Moisture Module & Probe */
        #mod-soil {
            top: 470px;
            left: 290px;
            width: 250px;
            border-color: #10b981;
            background: #0f241d;
        }

        .soil-pot {
            background: linear-gradient(180deg, #78350f 0%, #451a03 100%);
            border: 2px solid #92400e;
            border-radius: 6px;
            height: 85px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 6px;
        }

        .soil-water-level {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(56, 189, 248, 0.35);
            border-top: 2px solid #38bdf8;
            transition: height 0.5s ease;
        }

        .probe-prongs {
            width: 40px;
            height: 65px;
            background: repeating-linear-gradient(90deg, #cbd5e1 0, #cbd5e1 6px, transparent 6px, transparent 14px);
            z-index: 5;
            position: absolute;
            top: 10px;
        }

        /* 7. Relay Module */
        #mod-relay {
            top: 380px;
            left: 565px;
            width: 175px;
            border-color: #0284c7;
            background: #0a1f33;
        }

        .relay-cube {
            height: 65px;
            background: #0284c7;
            border-radius: 6px;
            border: 2px solid #38bdf8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.75rem;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4);
            transition: all 0.2s;
        }

        .relay-cube.active {
            background: #10b981;
            border-color: #34d399;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.7);
        }

        /* 8. Water Pump */
        #mod-pump {
            top: 25px;
            left: 620px;
            width: 120px;
            border-color: #64748b;
            background: #111827;
            text-align: center;
        }

        .pump-body {
            height: 65px;
            background: #1f2937;
            border-radius: 8px;
            border: 2px solid #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .pump-spray {
            position: absolute;
            top: -20px;
            right: -10px;
            display: none;
            font-size: 1.5rem;
            color: #38bdf8;
            animation: spray-spin 0.6s infinite alternate;
        }

        .pump-spray.active { display: block; }

        @keyframes spray-spin {
            from { transform: scale(0.9) rotate(-5deg); opacity: 0.7; }
            to { transform: scale(1.1) rotate(5deg); opacity: 1; }
        }

        /* 9. Smartphone Mockup (Right Canvas Section) */
        #mod-phone {
            top: 30px;
            right: 30px;
            width: 290px;
            background: #070b12;
            border: 3px solid #334155;
            border-radius: 28px;
            padding: 16px 12px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.8);
            z-index: 20;
        }

        .phone-inner-screen {
            background: #0e1626;
            border-radius: 18px;
            padding: 14px 10px;
            border: 1px solid #1e293b;
        }

        .phone-status-tag {
            background: #162238;
            border: 2px solid #334155;
            border-radius: 12px;
            padding: 14px 10px;
            text-align: center;
            margin-bottom: 12px;
        }

        .phone-big-txt {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            margin: 6px 0;
        }

        .phone-pump-badge {
            display: block;
            width: 75%;
            margin: 0 auto;
            padding: 10px;
            border-radius: 10px;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-align: center;
            transition: all 0.3s;
        }

        .phone-pump-badge.on {
            background: #10b981;
            color: #fff;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.7);
        }

        .phone-pump-badge.off {
            background: #1e293b;
            color: #64748b;
        }

        /* SVG Wire Layer */
        .wire-svg-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }

        .wire-path {
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.3s;
        }

        .wire-power { stroke: #ef4444; stroke-width: 3.5; }
        .wire-ground { stroke: #1e293b; stroke-width: 3.5; stroke-dasharray: 4 2; }
        .wire-signal { stroke: #10b981; stroke-width: 3; }
        .wire-relay-sw { stroke: #38bdf8; stroke-width: 3; }
        .wire-pump-hot { stroke: #f59e0b; stroke-width: 3.5; }

        /* Interactive Controls Bottom Panel */
        .control-dock {
            background: #0f172a;
            border: 2px solid var(--border-panel);
            border-radius: 1.25rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .badge-live-stream {
            animation: pulse-stream 1.5s infinite;
        }

        @keyframes pulse-stream {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
    </style>
</head>
<body>

<div class="container-fluid" style="max-width: 1440px;">
    <!-- Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3 pb-3 border-bottom border-secondary">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-uppercase px-3 py-1">Figure 4 Architecture</span>
                <span class="badge bg-success text-uppercase px-3 py-1" id="liveModeBadge">Stand-Alone Simulation</span>
                <span class="badge bg-info text-uppercase px-2 py-1"><i class="bi bi-cpu me-1"></i> ESP32 Direct Node</span>
            </div>
            <h2 class="h3 fw-bold mb-0">WBACFSPWI Prototype Interactive Simulator</h2>
            <p class="text-secondary small mb-0">
                Visual Interactive Twin of Figure 4: Solar Panel &bull; Controller &bull; Battery &bull; Breadboard &bull; ESP32 &bull; Soil Probe &bull; Relay &bull; Pump &bull; Smartphone
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/mobile.php" target="_blank" class="btn btn-outline-info btn-sm">
                <i class="bi bi-phone me-1"></i> Open Mobile Client &rarr;
            </a>
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Admin Portal &rarr;
            </a>
        </div>
    </div>

    <!-- Main Visual Canvas -->
    <div class="simulator-canvas-wrapper" id="canvasWrapper">
        <!-- SVG Connecting Wires (matching Figure 4 wire layout) -->
        <svg class="wire-svg-layer" id="wireSvg">
            <!-- 1. Solar Panel to Charge Controller -->
            <path d="M 250 65 L 300 65" class="wire-path wire-power" id="wireSolarPos" />
            <path d="M 250 85 L 300 85" class="wire-path wire-ground" id="wireSolarNeg" />

            <!-- 2. Battery to Charge Controller -->
            <path d="M 140 220 L 140 180 L 400 180 L 400 135" class="wire-path wire-power" />
            <path d="M 190 220 L 190 195 L 430 195 L 430 135" class="wire-path wire-ground" />

            <!-- 3. Controller to Breadboard Star Power Rails -->
            <path d="M 500 135 L 500 220" class="wire-path wire-power" />
            <path d="M 540 135 L 540 220" class="wire-path wire-ground" />

            <!-- 4. Breadboard to ESP32 (Power & Ground) -->
            <path d="M 330 290 L 330 330 L 160 330 L 160 380" class="wire-path wire-power" />
            <path d="M 370 290 L 370 345 L 180 345 L 180 380" class="wire-path wire-ground" />

            <!-- 5. ESP32 to Soil Moisture Module (AO GPIO 34 & Power Gate) -->
            <path d="M 250 450 L 290 495" class="wire-path wire-signal" />

            <!-- 6. ESP32 to Relay Module (Signal GPIO 26) -->
            <path d="M 260 410 L 565 410" class="wire-path wire-relay-sw" id="wireEspRelay" />

            <!-- 7. Relay Module to Pump (Switched 12V Power Line) -->
            <path d="M 640 380 L 640 120 L 660 90" class="wire-path wire-pump-hot" id="wireRelayPump" />
            <path d="M 700 90 L 700 180 L 540 180" class="wire-path wire-ground" />

            <!-- 8. Wireless WiFi Arc from ESP32 to Smartphone -->
            <path d="M 120 400 C 100 500, 780 500, 780 180" class="wire-path" stroke="#38bdf8" stroke-dasharray="6 4" stroke-width="2" opacity="0.6" />
        </svg>

        <!-- 1. SOLAR PANEL -->
        <div class="hardware-module" id="mod-solar">
            <div class="module-title">
                <span><i class="bi bi-sun text-warning me-1"></i> Solar Panel</span>
                <span class="badge bg-warning text-dark" id="panelWattTag">30W / 12V</span>
            </div>
            <div class="solar-grid">
                <div class="solar-sun-glow" id="sunGlow"></div>
                <?php for ($i=0; $i<12; $i++): ?><div class="solar-cell"></div><?php endfor; ?>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 small">
                <span class="text-secondary">Output:</span>
                <span class="mono fw-bold text-warning" id="solarOutVal">18.5 V</span>
            </div>
        </div>

        <!-- 2. SOLAR CHARGE CONTROLLER -->
        <div class="hardware-module" id="mod-controller">
            <div class="module-title">
                <span><i class="bi bi-cpu text-info me-1"></i> Solar Charge Controller</span>
                <span class="badge bg-primary">PWM 12V</span>
            </div>
            <div class="controller-lcd" id="controllerLcd">
                <div class="d-flex justify-content-between">
                    <span>SOLAR: <b id="lcdSolarV">18.5V</b></span>
                    <span id="lcdIcon"><i class="bi bi-arrow-right text-warning"></i> CHG</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>BATT: <b id="lcdBattV">12.4V</b></span>
                    <span class="text-info">[|||||]</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>LOAD: <b id="lcdLoad">ON</b></span>
                    <span>12.0V DC</span>
                </div>
            </div>
            <div class="controller-terminals">
                <div class="term-pair">
                    <div><span class="screw-head bg-danger"></span><br>PV+</div>
                    <div><span class="screw-head bg-dark"></span><br>PV-</div>
                </div>
                <div class="term-pair">
                    <div><span class="screw-head bg-danger"></span><br>BATT+</div>
                    <div><span class="screw-head bg-dark"></span><br>BATT-</div>
                </div>
                <div class="term-pair">
                    <div><span class="screw-head bg-danger"></span><br>LOAD+</div>
                    <div><span class="screw-head bg-dark"></span><br>LOAD-</div>
                </div>
            </div>
        </div>

        <!-- 3. 12V BATTERY -->
        <div class="hardware-module" id="mod-battery">
            <div class="module-title">
                <span><i class="bi bi-battery-charging text-success me-1"></i> 12V Battery</span>
                <span class="badge bg-secondary" id="batProfileTag">Lead-Acid</span>
            </div>
            <div class="battery-body">
                <div class="battery-terminals-top">
                    <div class="bat-post pos">+</div>
                    <div class="bat-post neg">-</div>
                </div>
                <div class="mono fw-bold fs-5 text-light" id="batDisplayV">12.40 V</div>
                <div class="text-secondary" style="font-size: 0.65rem;" id="batHealthLabel">Storage Normal</div>
            </div>
        </div>

        <!-- 4. BREADBOARD -->
        <div class="hardware-module" id="mod-breadboard">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-bold small text-dark"><i class="bi bi-grid-3x3-gap-fill me-1"></i> Solderless Breadboard</span>
                <span class="badge bg-dark text-white" style="font-size: 0.65rem;">Star Power Distribution</span>
            </div>
            <div class="bb-surface">
                <div class="bb-power-rails">
                    <span class="text-danger">+ 5V / 12V Bus</span>
                    <span class="text-danger">&bull; &bull; &bull; &bull; &bull; &bull; &bull;</span>
                </div>
                <div class="bb-power-rails gnd-rail">
                    <span class="text-primary">- Common GND Bus</span>
                    <span class="text-primary">&bull; &bull; &bull; &bull; &bull; &bull; &bull;</span>
                </div>
                <div class="d-flex justify-content-around text-muted mono" style="font-size: 0.6rem;">
                    <span>A</span><span>B</span><span>C</span><span>D</span><span>E</span>
                    <span>|</span>
                    <span>F</span><span>G</span><span>H</span><span>I</span><span>J</span>
                </div>
            </div>
        </div>

        <!-- 5. ESP32 MICROCONTROLLER -->
        <div class="hardware-module" id="mod-esp32">
            <div class="module-title">
                <span><i class="bi bi-cpu-fill text-primary me-1"></i> ESP32 Node</span>
                <span class="badge bg-info text-dark">WiFi 2.4G</span>
            </div>
            <div class="esp-chip">
                <div class="esp-metal-can">
                    <span>ESP-WROOM-32</span>
                </div>
                <div class="d-flex justify-content-between w-100 mt-2 px-1">
                    <div class="d-flex align-items-center gap-1">
                        <span class="esp-led led-red"></span>
                        <span style="font-size: 0.65rem;">PWR</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="esp-led led-blue" id="espStatusLed"></span>
                        <span style="font-size: 0.65rem;">RELAY (D26)</span>
                    </div>
                </div>
                <div class="text-secondary mono mt-1" style="font-size: 0.65rem;">
                    ADC: GPIO 34 (Soil) &bull; D26 (Relay)
                </div>
            </div>
        </div>

        <!-- 6. SOIL MOISTURE MODULE & PROBE -->
        <div class="hardware-module" id="mod-soil">
            <div class="module-title">
                <span><i class="bi bi-moisture text-success me-1"></i> Soil Moisture</span>
                <span class="badge bg-success" id="soilStatusBadge">DRY</span>
            </div>
            <div class="soil-pot">
                <div class="soil-water-level" id="soilWaterVis" style="height: 25%;"></div>
                <div class="probe-prongs"></div>
                <span class="mono fw-bold text-white position-relative" style="z-index: 10; font-size: 0.9rem;" id="soilReadingTxt">
                    25.0%
                </span>
            </div>
            <div class="text-secondary small mt-1 text-center" style="font-size: 0.65rem;">
                Root Zone Soil Probe
            </div>
        </div>

        <!-- 7. RELAY MODULE -->
        <div class="hardware-module" id="mod-relay">
            <div class="module-title">
                <span><i class="bi bi-toggle2-on text-info me-1"></i> Relay</span>
                <span class="badge bg-secondary" id="relayStateBadge">IDLE</span>
            </div>
            <div class="relay-cube" id="relayVisualCube">
                <span id="relayText">PUMP OFF</span>
                <span style="font-size: 0.65rem;" class="text-white-50">10A 250VAC / 30VDC</span>
            </div>
            <div class="d-flex justify-content-between mt-1 px-1" style="font-size: 0.65rem;">
                <span class="text-secondary">IN: GPIO 26</span>
                <span class="text-info">Active LOW</span>
            </div>
        </div>

        <!-- 8. WATER PUMP -->
        <div class="hardware-module" id="mod-pump">
            <div class="module-title justify-content-center">
                <span><i class="bi bi-water text-primary me-1"></i> 12V Pump</span>
            </div>
            <div class="pump-body">
                <i class="bi bi-fan fs-3 text-secondary" id="pumpFanIcon"></i>
                <div class="pump-spray" id="pumpSprayVis">💦</div>
            </div>
            <div class="text-secondary small mt-1" style="font-size: 0.65rem;" id="pumpMotorState">
                Stopped
            </div>
        </div>

        <!-- 9. SMARTPHONE (Direct Visual Twin of Figure 4 Screen) -->
        <div class="hardware-module" id="mod-phone">
            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <span class="text-secondary" style="font-size: 0.65rem;"><i class="bi bi-wifi text-success me-1"></i> ESP32-AP</span>
                <span class="text-secondary" style="font-size: 0.65rem;">100% <i class="bi bi-battery-full text-success"></i></span>
            </div>
            
            <div class="phone-inner-screen">
                <div class="phone-status-tag">
                    <div class="text-secondary small text-uppercase">Soil Condition</div>
                    <div class="phone-big-txt text-danger" id="simPhoneSoil">Dry</div>
                    <div class="text-white-50 small" id="simPhonePct">Moisture: 25.0%</div>
                </div>

                <div class="my-3 text-center">
                    <div class="text-secondary small text-uppercase mb-1">Water Pump</div>
                    <div class="phone-pump-badge on" id="simPhonePump">ON</div>
                </div>

                <div class="p-2 rounded bg-black border border-secondary text-secondary mono" style="font-size: 0.65rem;">
                    <div>BATT: <span class="text-white" id="simPhoneBat">12.4 V</span></div>
                    <div>SOLAR: <span class="text-warning" id="simPhoneSol">18.5 V</span></div>
                    <div>GATE: <span class="text-success">D26 HIGH</span></div>
                </div>
            </div>

            <div class="text-center mt-2">
                <span class="badge bg-dark text-secondary" style="font-size: 0.65rem;">Figure 4 Mobile View</span>
            </div>
        </div>
    </div>

    <!-- Interactive Simulator Controls & Backend Sync Dock -->
    <div class="control-dock">
        <div class="row g-4">
            <!-- Left Controls: Environmental Sliders -->
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-primary"></i> Environmental & Sensor Inputs
                </h5>
                
                <!-- Soil Moisture Slider -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <label for="sliderSoil" class="fw-bold">
                            <i class="bi bi-moisture text-success me-1"></i> Soil Moisture Level:
                        </label>
                        <span class="mono fw-bold text-success fs-6" id="labelSoilVal">25.0 % (Dry &mdash; Triggering Pump)</span>
                    </div>
                    <input type="range" class="form-range" id="sliderSoil" min="0" max="100" step="0.5" value="25.0" oninput="updateSimInputs()">
                    <div class="d-flex justify-content-between text-secondary" style="font-size: 0.7rem;">
                        <span>0% Bone Dry</span>
                        <span class="text-danger fw-bold">&lt; 40% Refill Trigger (Pump ON)</span>
                        <span class="text-success fw-bold">&gt;= 50% Target (Pump OFF)</span>
                        <span>100% Saturated</span>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Solar Sunlight Slider -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between small mb-1">
                            <label for="sliderSolar" class="fw-bold">
                                <i class="bi bi-sun text-warning me-1"></i> Solar Irradiation:
                            </label>
                            <span class="mono fw-bold text-warning" id="labelSolarVal">18.5 V</span>
                        </div>
                        <input type="range" class="form-range" id="sliderSolar" min="0" max="22" step="0.2" value="18.5" oninput="updateSimInputs()">
                        <div class="d-flex justify-content-between text-secondary" style="font-size: 0.7rem;">
                            <span>0V Night</span>
                            <span>12V Charging</span>
                            <span>22V Peak Sun</span>
                        </div>
                    </div>

                    <!-- Battery Voltage Slider -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between small mb-1">
                            <label for="sliderBat" class="fw-bold">
                                <i class="bi bi-battery-charging text-info me-1"></i> Battery Voltage:
                            </label>
                            <span class="mono fw-bold text-info" id="labelBatVal">12.40 V</span>
                        </div>
                        <input type="range" class="form-range" id="sliderBat" min="9.0" max="14.4" step="0.1" value="12.4" oninput="updateSimInputs()">
                        <div class="d-flex justify-content-between text-secondary" style="font-size: 0.7rem;">
                            <span class="text-danger">&lt; 10.0V Lockout</span>
                            <span>12.4V Resting</span>
                            <span class="text-warning">14.4V Float</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Presets -->
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <span class="text-secondary small d-flex align-items-center">Presets:</span>
                    <button class="btn btn-outline-danger btn-sm" onclick="setPreset(20, 18.5, 12.4)">
                        Dry Field (Irrigate)
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="setPreset(60, 19.0, 12.6)">
                        Saturated Field (Idle)
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="setPreset(30, 0.0, 9.8)">
                        Night &amp; Low Battery Lockout
                    </button>
                </div>
            </div>

            <!-- Right Controls: Backend Sync & Autonomous Rules -->
            <div class="col-lg-5 border-start border-secondary ps-lg-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-broadcast text-info"></i> Real Backend Telemetry Sync
                </h5>

                <div class="p-3 rounded bg-black border border-secondary mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="backendSyncSwitch" onchange="toggleBackendSync()">
                        <label class="form-check-label fw-bold text-light" for="backendSyncSwitch">
                            Broadcast to Live XAMPP Database
                        </label>
                    </div>
                    <p class="text-secondary small mb-2">
                        When enabled, this simulator acts as the physical ESP32 and sends real JSON packets to <code>POST /api/device/report.php</code> every 2 seconds.
                    </p>
                    <div class="mono small text-secondary" id="syncStatusLine">
                        Status: <span class="text-warning">Standby (Local Sandbox)</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>/admin/telemetry_test.php" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-activity me-1"></i> Live Stream Inspector
                    </a>
                    <a href="<?= BASE_URL ?>/arduino/figure4_prototype_guide.html" target="_blank" class="btn btn-outline-info btn-sm flex-fill">
                        <i class="bi bi-diagram-2 me-1"></i> Wiring Guide
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    const DEVICE_API_KEY = 'dev-local-device-key';

    // Simulation State
    let simMoisture = 25.0;
    let simSolar = 18.5;
    let simBattery = 12.4;
    let pumpActive = true;
    let backendSyncActive = false;
    let syncIntervalId = null;

    // Safety Interlocks
    const MIN_BATT_LOCKOUT = 10.0;
    const REFILL_MIN_PCT = 40.0;
    const TARGET_MAX_PCT = 50.0;

    function updateSimInputs() {
        simMoisture = parseFloat(document.getElementById('sliderSoil').value);
        simSolar = parseFloat(document.getElementById('sliderSolar').value);
        simBattery = parseFloat(document.getElementById('sliderBat').value);

        // Update Labels
        let soilMsg = 'Optimal';
        if (simMoisture < REFILL_MIN_PCT) soilMsg = 'Dry — Triggering Pump';
        else if (simMoisture >= TARGET_MAX_PCT) soilMsg = 'Moist — Target Reached';
        document.getElementById('labelSoilVal').textContent = `${simMoisture.toFixed(1)} % (${soilMsg})`;
        document.getElementById('labelSolarVal').textContent = `${simSolar.toFixed(1)} V`;
        document.getElementById('labelBatVal').textContent = `${simBattery.toFixed(2)} V`;

        evaluateAutonomousLogic();
        renderSimVisuals();
    }

    function setPreset(moist, solar, bat) {
        document.getElementById('sliderSoil').value = moist;
        document.getElementById('sliderSolar').value = solar;
        document.getElementById('sliderBat').value = bat;
        updateSimInputs();
    }

    function evaluateAutonomousLogic() {
        // Battery Lockout Check
        if (simBattery < MIN_BATT_LOCKOUT) {
            pumpActive = false;
            return;
        }

        // Hysteresis Band
        if (simMoisture < REFILL_MIN_PCT) {
            pumpActive = true;
        } else if (simMoisture >= TARGET_MAX_PCT) {
            pumpActive = false;
        }
        // If between 40% and 50%, state is maintained
    }

    function renderSimVisuals() {
        // 1. Solar Visuals
        document.getElementById('solarOutVal').textContent = `${simSolar.toFixed(1)} V`;
        document.getElementById('sunGlow').style.opacity = Math.min(1.0, simSolar / 18.0);
        document.getElementById('lcdSolarV').textContent = `${simSolar.toFixed(1)}V`;

        // 2. Battery Visuals
        document.getElementById('batDisplayV').textContent = `${simBattery.toFixed(2)} V`;
        document.getElementById('lcdBattV').textContent = `${simBattery.toFixed(1)}V`;
        const batHealthEl = document.getElementById('batHealthLabel');
        if (simBattery < MIN_BATT_LOCKOUT) {
            batHealthEl.textContent = 'LOW VOLTAGE LOCKOUT';
            batHealthEl.className = 'text-danger fw-bold';
        } else {
            batHealthEl.textContent = 'Storage Normal';
            batHealthEl.className = 'text-secondary';
        }

        // 3. Controller LCD
        document.getElementById('lcdLoad').textContent = pumpActive ? 'ON (PUMP)' : 'STANDBY';

        // 4. Soil Visuals
        document.getElementById('soilWaterVis').style.height = `${Math.min(100, Math.max(10, simMoisture))}%`;
        document.getElementById('soilReadingTxt').textContent = `${simMoisture.toFixed(1)}%`;
        const soilBadge = document.getElementById('soilStatusBadge');
        if (simMoisture < REFILL_MIN_PCT) {
            soilBadge.className = 'badge bg-danger';
            soilBadge.textContent = 'DRY';
        } else if (simMoisture >= TARGET_MAX_PCT) {
            soilBadge.className = 'badge bg-info';
            soilBadge.textContent = 'WET';
        } else {
            soilBadge.className = 'badge bg-success';
            soilBadge.textContent = 'OPTIMAL';
        }

        // 5. Relay Visuals
        const relayCube = document.getElementById('relayVisualCube');
        const relayBadge = document.getElementById('relayStateBadge');
        const relayText = document.getElementById('relayText');
        const espLed = document.getElementById('espStatusLed');

        if (pumpActive) {
            relayCube.className = 'relay-cube active';
            relayBadge.className = 'badge bg-success';
            relayBadge.textContent = 'CLOSED (ON)';
            relayText.textContent = 'PUMP ENGAGED';
            espLed.className = 'esp-led led-blue';
        } else {
            relayCube.className = 'relay-cube';
            relayBadge.className = 'badge bg-secondary';
            relayBadge.textContent = 'IDLE (OFF)';
            relayText.textContent = 'PUMP OFF';
            espLed.className = 'esp-led led-blue opacity-25';
        }

        // 6. Pump Visuals
        const fanIcon = document.getElementById('pumpFanIcon');
        const sprayVis = document.getElementById('pumpSprayVis');
        const pumpMotorState = document.getElementById('pumpMotorState');

        if (pumpActive) {
            fanIcon.className = 'bi bi-fan fs-3 text-info spinner-border spinner-border-sm';
            sprayVis.className = 'pump-spray active';
            pumpMotorState.textContent = '12V DC Active (Irrigating)';
            pumpMotorState.className = 'text-info small mt-1';
        } else {
            fanIcon.className = 'bi bi-fan fs-3 text-secondary';
            sprayVis.className = 'pump-spray';
            pumpMotorState.textContent = 'Stopped';
            pumpMotorState.className = 'text-secondary small mt-1';
        }

        // 7. Phone Screen (Direct Figure 4 Match)
        const phoneSoil = document.getElementById('simPhoneSoil');
        const phonePump = document.getElementById('simPhonePump');
        document.getElementById('simPhonePct').textContent = `Moisture: ${simMoisture.toFixed(1)}%`;
        document.getElementById('simPhoneBat').textContent = `${simBattery.toFixed(1)} V`;
        document.getElementById('simPhoneSol').textContent = `${simSolar.toFixed(1)} V`;

        if (simMoisture < REFILL_MIN_PCT) {
            phoneSoil.textContent = 'Dry';
            phoneSoil.className = 'phone-big-txt text-danger';
        } else {
            phoneSoil.textContent = 'Wet';
            phoneSoil.className = 'phone-big-txt text-success';
        }

        if (pumpActive) {
            phonePump.textContent = 'ON';
            phonePump.className = 'phone-pump-badge on';
        } else {
            phonePump.textContent = 'OFF';
            phonePump.className = 'phone-pump-badge off';
        }

        // Wire glows
        document.getElementById('wireRelayPump').style.stroke = pumpActive ? '#10b981' : '#334155';
    }

    // Backend Live Transmission
    function toggleBackendSync() {
        const sw = document.getElementById('backendSyncSwitch');
        backendSyncActive = sw.checked;
        const statusEl = document.getElementById('syncStatusLine');
        const modeBadge = document.getElementById('liveModeBadge');

        if (backendSyncActive) {
            statusEl.innerHTML = 'Status: <span class="text-success fw-bold">Broadcasting Live Packets (2s Interval)</span>';
            modeBadge.className = 'badge bg-danger text-uppercase px-3 py-1 badge-live-stream';
            modeBadge.textContent = 'Live Backend Ingestion';
            sendTelemetryPacket();
            syncIntervalId = setInterval(sendTelemetryPacket, 2000);
        } else {
            clearInterval(syncIntervalId);
            statusEl.innerHTML = 'Status: <span class="text-warning">Standby (Local Sandbox)</span>';
            modeBadge.className = 'badge bg-success text-uppercase px-3 py-1';
            modeBadge.textContent = 'Stand-Alone Simulation';
        }
    }

    async function sendTelemetryPacket() {
        if (!backendSyncActive) return;
        try {
            const payload = {
                soil_moisture: Math.round(simMoisture * 10) / 10,
                battery_voltage: Math.round(simBattery * 100) / 100,
                solar_output: Math.round(simSolar * 10) / 10,
                pump_state: pumpActive ? 'on' : 'off'
            };

            const res = await fetch(`${BASE_URL}/api/device/report.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-Key': DEVICE_API_KEY
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.command) {
                // Handle remote override from admin dashboard
                if (data.command === 'PUMP_ON') pumpActive = true;
                if (data.command === 'PUMP_OFF') pumpActive = false;
                renderSimVisuals();
            }
        } catch (e) {
            console.warn('Backend sync packet error:', e);
        }
    }

    // Initial render
    updateSimInputs();
</script>

</body>
</html>
