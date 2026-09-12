# WBACFSPWI — Master System Memory & Inter-File Synchronization Protocol

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **System Architecture:** Arduino Uno R3 Actuator/Sensor Controller + ESP32/ESP8266 Telemetry Node + PHP/MySQL Web Portal  
> **Target Application:** Autonomous Miniature Rice Field Water Maintenance System  
> **Single Source of Truth (SSOT) Version:** 1.0.0  
> **Last Verified:** September 2026  

---

## 1. System Overview & Architecture

WBACFSPWI consists of three synchronized tiers:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                   HARDWARE TIER                                        │
│                                                                                        │
│  [30W Solar Panel] ──► [Charge Controller] ──► [3S 18650 Battery Pack (11.1V-12.6V)]   │
│                                                          │                             │
│                                                          ▼                             │
│                                              [LM2596 Buck (12V -> 5V)]                 │
│                                                          │                             │
│       ┌──────────────────────────────────────────────────┴────────────────────┐        │
│       ▼                                                                       ▼        │
│  [Arduino Uno R3 MCU]                                                 [Star 5V Rails]  │
│    ├── A0: Capacitive Soil Moisture v1.2 (Root Zone, D8 power-gated)          │        │
│    ├── A1: HW-080 Surface Water Level Sensor (Standing Water Depth)           │        │
│    ├── A2: Battery Resistor Divider (100kΩ / 33kΩ, Factor: 4.0303)            │        │
│    ├── A3: Solar Resistor Divider (100kΩ / 20kΩ, Factor: 6.0000)              │        │
│    ├── D7: 5V Relay Module (Active LOW, controls 12V DC Water Pump)           │        │
│    └── D8: Power Gate Digital Pin (anti-corrosion power gating)               │        │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                           │ UART Serial Telemetry / WiFi Bridge
                                           ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 COMMUNICATION TIER                                     │
│  [ESP32 / ESP8266 IoT Node]                                                            │
│    ├── GET  /api/device/pull-schedule.php (sync schedules, X-API-Key header)          │
│    └── POST /api/device/report.php        (push sensor & pump telemetry)              │
└──────────────────────────────────────────┬─────────────────────────────────────────────┘
                                           │ HTTP REST (JSON)
                                           ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                  WEB & DATA TIER                                       │
│  [PHP 8.2 Backend + MySQL Database `wbacfspwi`]                                        │
│    ├── Core Config: config/bootstrap.php, config/device.php, config/database.php       │
│    ├── Database Tables: users, schedules, sensor_readings, irrigation_events,          │
│    │                    alerts, audit_logs, overrides                                 │
│    ├── Models: User, Schedule, SensorReading, IrrigationEvent, Alert, AuditLog         │
│    └── Web UI: /admin/dashboard.php, /admin/schedule.php, /admin/logs.php,             │
│                /admin/reports.php, /admin/users.php, /admin/profile.php                │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Master Inter-File Synchronization Dependency Matrix

Whenever a file in the **Source File** column is modified, **ALL** files in the **Connected Files to Update** column **MUST BE SYNCHRONIZED IMMEDIATELY**.

| Component / Subsystem | Source File Modified | Connected Files to Update & Synchronize | Required Synchronized Constants / Fields |
| :--- | :--- | :--- | :--- |
| **Capacitive Soil Sensor (Root Zone - A0)** | `arduino/01_soil_root_capacitive_test/` | • `arduino/07_dual_sensor_pump_integration_test/`<br>• `arduino/08_dc_adapter_presentation_test/`<br>• `arduino/wbacfspwi_arduino_controller/`<br>• `arduino/CALIBRATION_REGISTRY.md`<br>• `.agents/rules/arduino_calibration_sync.md` | `SOIL_AIR_RAW = 417`<br>`SOIL_WATER_RAW = 153`<br>Multi-sample averaging (16x)<br>Power gate pin `D8` |
| **HW-080 Surface Water Level (A1)** | `arduino/02_surface_water_level_test/` | • `arduino/07_dual_sensor_pump_integration_test/`<br>• `arduino/08_dc_adapter_presentation_test/`<br>• `arduino/wbacfspwi_arduino_controller/`<br>• `arduino/wbacfspwi_arduino_controller/wiring_guide.html`<br>• `arduino/CALIBRATION_REGISTRY.md`<br>• `arduino/Pinout_and_Schematic.md`<br>• `.agents/rules/arduino_calibration_sync.md` | `HW080_RAW_DRY = 1020`<br>`HW080_RAW_MID = 410`<br>`HW080_RAW_WET = 355`<br>3-point piecewise ruler calibration curve |
| **Battery Divider (A2)** | `arduino/04_battery_voltage_test/` | • `arduino/06_solar_charger_battery_test/`<br>• `arduino/wbacfspwi_arduino_controller/`<br>• `arduino/CALIBRATION_REGISTRY.md`<br>• `arduino/Pinout_and_Schematic.md` | Resistors: $100\text{k}\Omega / 33\text{k}\Omega$<br>Factor: `4.0303`<br>`BATT_MIN_LOCKOUT = 10.00V`<br>`BATT_RESUME_VOLTS = 10.50V` |
| **Solar Divider (A3)** | `arduino/05_solar_voltage_test/` | • `arduino/06_solar_charger_battery_test/`<br>• `arduino/wbacfspwi_arduino_controller/`<br>• `arduino/CALIBRATION_REGISTRY.md`<br>• `arduino/Pinout_and_Schematic.md` | Resistors: $100\text{k}\Omega / 20\text{k}\Omega$<br>Factor: `6.0000`<br>`SOLAR_SUN_THRESHOLD = 12.0V` |
| **Irrigation Logic & Safety Thresholds** | `arduino/wbacfspwi_arduino_controller/` | • `arduino/07_dual_sensor_pump_integration_test/`<br>• `arduino/08_dc_adapter_presentation_test/`<br>• `arduino/wbacfspwi_arduino_controller/wiring_guide.html`<br>• `arduino/CALIBRATION_REGISTRY.md`<br>• `test_integration/STANDALONE_INTEGRATION_GUIDE.md`<br>• `.agents/rules/arduino_calibration_sync.md` | `WATER_TARGET_MAX = 50.0%`<br>`WATER_REFILL_MIN = 45.0%`<br>`MIN_PUMP_RUN_MS = 5000ms`<br>`SETTLING_DELAY_MS = 10000ms`<br>`MAX_PUMP_RUN_MS = 180000ms`<br>`PUMP_COOLDOWN_MS = 60000ms` |
| **Device Authentication & API Key** | `config/device.php` | • `firmware/wbacfspwi_node/wbacfspwi_node.ino`<br>• `firmware/wbacfspwi_esp8266_node/wbacfspwi_esp8266_node.ino`<br>• `arduino/09_esp8266_wifi_bridge_test/09_esp8266_wifi_bridge_test.ino`<br>• `src/helpers/DeviceAuth.php`<br>• `docs/checklist.md`<br>• `README.md` | `DEVICE_API_KEY = 'dev-local-device-key'`<br>Header: `X-API-Key`<br>Endpoint: `/api/device/report.php`<br>Endpoint: `/api/device/pull-schedule.php` |
| **NodeMCU ESP8266 WiFi Bridge** | `arduino/09_esp8266_wifi_bridge_test/` | • `firmware/wbacfspwi_esp8266_node/`<br>• `arduino/wbacfspwi_arduino_controller/`<br>• `arduino/wbacfspwi_arduino_controller/wiring_guide.html`<br>• `arduino/HARDWARE_STARTUP_GUIDE.md`<br>• `arduino/Pinout_and_Schematic.md`<br>• `SYSTEM_MEMORY.md` | Board: NodeMCU v2/v3 (ESP-12E / CP2102)<br>Pins: Arduino D10 (TX) -> NodeMCU D1 (RX) via 1k/2k divider; NodeMCU D2 (TX) -> Arduino D9 (RX)<br>Power: LM2596 Star 5V Rail -> NodeMCU `Vin`<br>Baud: 9600 baud SoftwareSerial bridge |
| **GSM Module SMS Alert System** | `arduino/10_gsm_sms_irrigation_alert_test/` | • `arduino/10_gsm_sms_irrigation_alert_test/10_gsm_sms_irrigation_alert_test.ino`<br>• `arduino/10_gsm_sms_irrigation_alert_test/wiring_guide.html`<br>• `arduino/wbacfspwi_arduino_controller/wiring_guide.html`<br>• `arduino/HARDWARE_STARTUP_GUIDE.md`<br>• `arduino/Pinout_and_Schematic.md`<br>• `SYSTEM_MEMORY.md` | Board: SIM800L / SIM900 GSM/GPRS Module<br>Pins: Arduino D2 (RX) <- GSM TXD; Arduino D3 (TX) -> GSM RXD via 1k/2k divider<br>Power: 3.7V - 4.4V (4.0V nominal, 2A burst) + 1000µF buffer cap<br>Automated SMS triggers: Irrigation Started (<45%), Stopped (>=50%), and Restarted (<45% again) |
| **Battery Profiles & Thresholds** | `config/device.php` | • `src/models/SensorReading.php`<br>• `public/api/device/report.php`<br>• `firmware/wbacfspwi_node/wbacfspwi_node.ino`<br>• `firmware/wbacfspwi_esp8266_node/wbacfspwi_esp8266_node.ino` | 12V Motorcycle Lead-Acid/AGM (default): Min `10.5V`, Max `14.4V`, Low Alert `10.8V`, High Alert `14.8V`<br>3S Li-ion profile: Min `10.0V`, Max `12.6V`, Low Alert `10.0V`, High Alert `13.0V`<br>6V SLA profile: Min `5.4V`, Max `7.2V`, Low Alert `5.8V`, High Alert `7.4V` |
| **Database Schema & Migrations** | `database/schema.sql` | • `database/migrations/`<br>• `src/models/*.php`<br>• `public/api/admin/dashboard-data.php`<br>• `public/api/device/report.php` | Tables: `users`, `schedules`, `sensor_readings`, `irrigation_events`, `alerts`, `audit_logs`, `overrides`<br>Sequential migration numbers: `001_`, `002_`, `003_` |
| **Admin Navigation & Pages** | `public/admin/` (adding/modifying pages) | • `public/admin/partials/sidebar.php`<br>• `$activePage` variable on target page<br>• User role permission check in `guard.php` or top of file | `$navItems` keys and labels: `dashboard`, `schedule`, `logs`, `reports`, `users`, `profile` |

---

## 3. Hardware Pinout & Star Wiring Single Source of Truth

All hardware sketches and diagrams adhere strictly to these physical pin connections:

| Pin | Function | Direction | Electrical Interface / Characteristics |
| :---: | :--- | :---: | :--- |
| **A0** | Capacitive Soil Moisture Sensor v1.2 | Analog IN | 0.0V–3.0V output. VCC gated through D8. Raw: 417 (air) to 153 (submerged). |
| **A1** | HW-080 Surface Water Ponding Sensor | Analog IN | 0.0V–5.0V output from LM393. Raw: 1020 (dry air), 410 (mid 50%), 355 (full 100%). |
| **A2** | 3S 18650 Battery Voltage Tap | Analog IN | $100\text{k}\Omega / 33\text{k}\Omega$ divider. Ratio: `4.0303`. Max 3.125V at 12.6V. |
| **A3** | 30W Solar Panel Voltage Tap | Analog IN | $100\text{k}\Omega / 20\text{k}\Omega$ divider. Ratio: `6.0000`. Max 3.667V at 22.0V Voc. |
| **D2** | SoftwareSerial RX (from GSM) | Digital IN | Connects directly to GSM `5VT` / `TXD` (2.8V–5.0V TTL safe direct read). |
| **D3** | SoftwareSerial TX (to GSM) | Digital OUT | Connects directly to SIM900A `5VR` (onboard level shifter) or via 1kΩ / 2kΩ divider for raw 3.3V SIM800L. |
| **D7** | 5V Relay Control (DC Water Pump) | Digital OUT | **Active LOW** (`LOW` = Relay Engaged / Pump ON; `HIGH` = Pump OFF). |
| **D8** | Capacitive Sensor Power Gate | Digital OUT | `HIGH` during reading, `LOW` between samples to prevent electrolytic corrosion. |
| **D9** | SoftwareSerial RX (from NodeMCU) | Digital IN | Connects to NodeMCU Pin `D2` (TX, 3.3V logic is safe for Arduino 5V input). |
| **D10**| SoftwareSerial TX (to NodeMCU) | Digital OUT | Connects to NodeMCU Pin `D1` (RX) via 1kΩ / 2kΩ resistor divider (5V $\rightarrow$ 3.3V). |
| **D13**| Built-in Status LED | Digital OUT | Mirrors pump status and system heartbeat / fault codes. |

---

## 4. Operational Control Decision Logic (3-Layer Safety Net)

The standalone Arduino Uno controller (`wbacfspwi_arduino_controller.ino`) and integration sketches operate on a 3-layer protection model:

1. **Layer 1: Hysteresis Band**
   - **PUMP START (ON):** Surface Water Level drops $< 45.0\%$ (and Battery $\ge 10.00\text{V}$).
   - **PUMP STOP (OFF):** Surface Water Level reaches $\ge 50.0\%$.
   - **Deadband / Buffer:** $45.0\% \le \text{Water Level} < 50.0\%$ maintains previous state (no rapid cycling).

2. **Layer 2: Anti-Splash Minimum Runtime (`MIN_PUMP_RUN_MS = 5000ms`)**
   - Once activated, the pump runs for at least **5 seconds** before target-reached shutoff is permitted.
   - Prevents immediate pump shutoff caused by turbulent water surface ripples or spray splashing onto the HW-080 probe.

3. **Layer 3: Wave Settling Window (`SETTLING_DELAY_MS = 10000ms`)**
   - When the pump stops after hitting $\ge 50.0\%$, the system enters a **10-second settling window**.
   - Sensors pause irrigation decisions while physical wave action dissipates.
   - If water settles $\ge 50.0\%$, state is marked STABLE. If water level settles $< 50.0\%$, pump refills to true target.

4. **Safety Interlocks:**
   - **Continuous Run Cap:** `MAX_PUMP_RUN_MS = 180000ms` (3 minutes maximum runtime).
   - **Mandatory Cooldown:** `PUMP_COOLDOWN_MS = 60000ms` (1 minute cooldown before re-triggering).
   - **Low Battery Cutoff:** `BATT_MIN_LOCKOUT = 10.00V` (inhibits pump to prevent deep cell discharge).
   - **Battery Resume Voltage:** `BATT_RESUME_VOLTS = 10.50V` (hysteresis voltage before clearing lockout).

---

## 5. API Data Contracts & Endpoint Specifications

### Endpoint 1: Device Telemetry Report
- **URL:** `POST /api/device/report.php`
- **Headers:** `Content-Type: application/json`, `X-API-Key: <DEVICE_API_KEY>`
- **Request Body:**
  ```json
  {
    "soil_moisture": 45.2,
    "water_level": 50.1,
    "battery_voltage": 12.1,
    "solar_output": 18.5,
    "pump_state": "on",
    "schedule_id": 3
  }
  ```
- **Response Body:**
  ```json
  {
    "status": "ok",
    "alerts_created": []
  }
  ```

### Endpoint 2: Device Pull Schedule
- **URL:** `GET /api/device/pull-schedule.php`
- **Headers:** `X-API-Key: <DEVICE_API_KEY>`
- **Response Body:**
  ```json
  {
    "schedules": [
      {
        "id": 1,
        "label": "Morning Irrigation",
        "start_time": "06:00",
        "duration_minutes": 15,
        "days_of_week": ["mon", "wed", "fri"]
      }
    ]
  }
  ```

### Endpoint 3: Admin Dashboard Live Stream
- **URL:** `GET /api/admin/dashboard-data.php`
- **Session:** Authenticated session (`Auth::requireLogin()`)
- **Returns:** Latest reading, 8-day trends, today schedules, active alerts.

### Endpoint 4: Admin Live Telemetry Inspector
- **URL:** `GET|POST /api/admin/telemetry-live.php`
- **Session:** Authenticated session (`Auth::requireRole()`)
- **Page:** [`public/admin/telemetry_test.php`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public/admin/telemetry_test.php)
- **Features:** Real-time hardware packet stream (1.5s poll), raw JSON inspector, test packet injection, 0-data purge.

### Endpoint 5: Standalone Public Telemetry Stream
- **URL:** `GET /api/device/telemetry-public.php`
- **Page:** [`public/telemetry_monitor.php`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public/telemetry_monitor.php)
- **Features:** Direct lightweight mobile/desktop telemetry viewer without login requirements for immediate field testing.

### Endpoint 6: Remote Pump Manual Override API
- **URL:** `POST /api/admin/pump-control.php`
- **Session:** Authenticated session (`Auth::requireRole(['super_admin', 'admin'])`)
- **Action values:** `on` (Force ON), `off` (Force OFF), `auto` (Resume Auto)
- **Model:** [`src/models/Override.php`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/src/models/Override.php)
- **Device Delivery:** Embedded in HTTP 200 response of `/api/device/report.php` as `"command": "PUMP_ON" | "PUMP_OFF" | "PUMP_AUTO"`. NodeMCU receives response and writes command to Arduino Uno SoftwareSerial (D1/D2 <-> 9/10).
- **Arduino Safety Override:** Manual state overrides automatic water level thresholds, while preserving low-battery lockout (<10.0V) and thermal/runtime safety limits.

---

## 6. How to Verify Full Synchronization

Run the automated verification suite from the project root at any time:

```bash
php scripts/verify_sync.php
```

If all 99 inter-file tests pass, the system is 100% synchronized. If any check fails, the test script pinpoints the exact file, constant, and line that is out of sync.
