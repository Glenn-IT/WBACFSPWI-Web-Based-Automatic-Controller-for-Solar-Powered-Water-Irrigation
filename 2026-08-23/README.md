# WBACFSPWI — System Audit & Hardware Troubleshooting Log
**Date:** August 23, 2026 (`2026-08-23`)  
**Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
**Status:** Pre-Integration Hardware Verification & Firmware-to-Web Alignment  

---

## 📑 Table of Contents
1. [Troubleshooting Case Log (August 23, 2026)](#1-troubleshooting-case-log-august-23-2026)
   - [Case 01: Test Integration Panel Pump Override vs Hardware Response](#case-01-test-integration-panel-pump-override-vs-hardware-response)
   - [Case 02: Test 01 Capacitive Soil Moisture Sensor Calibration](#case-02-test-01-capacitive-soil-moisture-sensor-calibration)
2. [Complete System Files & Functions Audit](#2-complete-system-files--functions-audit)
   - [Backend & Database Structure](#a-backend--database-structure)
   - [API Endpoints](#b-api-endpoints)
   - [Admin Web Panel & Frontend](#c-admin-web-panel--frontend)
   - [Firmware & Arduino Controllers](#d-firmware--arduino-controllers)
3. [Pre-Integration Discrepancies & Action Items](#3-pre-integration-discrepancies--action-items)
4. [Step-by-Step Hardware-to-Software Integration Plan](#4-step-by-step-hardware-to-software-integration-plan)

---

## 1. Troubleshooting Case Log (August 23, 2026)

### Case 01: Test Integration Panel Pump Override vs Hardware Response
* **Symptoms:** Clicking "Force Pump ON" / "Force Pump OFF" in the `test_integration` directory did not trigger the physical water pump relay.
* **Root Cause Analysis:**
  1. `test_integration/index.html` is a 100% standalone, client-side browser JavaScript simulator designed for UI and decision logic validation without hardware. It has no active USB/WebSerial or HTTP connection to the physical microcontroller.
  2. In software, if battery voltage is $< 10.0\text{V}$ or surface water level is $\ge 80\%$, the built-in safety interlock automatically disengages the pump even in manual override.
* **Resolution & Testing Procedure:**
  - Physical pump testing must be executed via [`arduino/03_relay_pump_test/03_relay_pump_test.ino`](../arduino/03_relay_pump_test/03_relay_pump_test.ino) or full controller [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino).

---

### Case 02: Test 01 Capacitive Soil Moisture Sensor Calibration
* **Symptoms:**
  - Sensor in dry air reported **`65.5% Moisture`** (`Raw ADC: 417`).
  - Sensor with tip touched into water immediately saturated to **`100.0% Moisture`** (`Raw ADC: 153` fully submerged).
* **Root Cause Analysis:**
  - The sketch used uncalibrated factory defaults (`SOIL_AIR_RAW = 620`, `SOIL_WATER_RAW = 310`).
  - Formula: $\text{Moisture \%} = 100 \times \frac{\text{SOIL\_AIR\_RAW} - \text{Raw}}{\text{SOIL\_AIR\_RAW} - \text{SOIL\_WATER\_RAW}}$
  - In dry air: $100 \times \frac{620 - 417}{620 - 310} = 65.5\%$
  - In water: Raw $153 < 310$, clamping to $100.0\%$.
* **Applied Fix & Calibrated Parameters:**
  - `SOIL_AIR_RAW`: **`417`** (0.0% moisture in dry air)
  - `SOIL_WATER_RAW`: **`153`** (100.0% moisture fully submerged)
* **Updated Files:**
  - [`arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino`](../arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino)
  - [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino)
  - [`arduino/Pinout_and_Schematic.md`](../arduino/Pinout_and_Schematic.md)
  - [`arduino/wbacfspwi_arduino_controller/wiring_guide.html`](../arduino/wbacfspwi_arduino_controller/wiring_guide.html)

---

## 2. Complete System Files & Functions Audit

### A. Backend & Database Structure

| File Path | Primary Functions / Purpose | Status |
| :--- | :--- | :--- |
| [`config/bootstrap.php`](../config/bootstrap.php) | App init, autoloader (`models`, `services`, `controllers`), sets `BASE_URL`, starts session. | Verified ✅ |
| [`config/database.php`](../config/database.php) | PDO singleton database connection handler. | Verified ✅ |
| [`config/device.php`](../config/device.php) | `DEVICE_API_KEY`, alert thresholds for low moisture, battery voltage levels. | Requires Sync ⚠️ |
| [`config/security_questions.php`](../config/security_questions.php) | Security question catalog for password recovery. | Verified ✅ |
| [`database/schema.sql`](../database/schema.sql) | DDL for `users`, `schedules`, `sensor_readings`, `irrigation_events`, `alerts`, `audit_logs`. | Verified ✅ |
| [`src/helpers/Auth.php`](../src/helpers/Auth.php) | Authentication session management, role checks (`super_admin`, `admin`, `viewer`). | Verified ✅ |
| [`src/helpers/Csrf.php`](../src/helpers/Csrf.php) | CSRF token generation and validation. | Verified ✅ |
| [`src/helpers/DeviceAuth.php`](../src/helpers/DeviceAuth.php) | Verifies `X-API-Key` header from IoT hardware node. | Verified ✅ |
| [`src/helpers/RateLimiter.php`](../src/helpers/RateLimiter.php) | IP rate-limiting for login attempts and public routes. | Verified ✅ |
| [`src/models/SensorReading.php`](../src/models/SensorReading.php) | CRUD for sensor telemetry, trend calculations, battery percentage conversion. | Verified ✅ |
| [`src/models/IrrigationEvent.php`](../src/models/IrrigationEvent.php) | Tracks irrigation cycles (`start`, `complete`, `findRunning`). | Verified ✅ |
| [`src/models/Schedule.php`](../src/models/Schedule.php) | Manages automated irrigation schedules. | Verified ✅ |
| [`src/models/Alert.php`](../src/models/Alert.php) | System alert generation, recent alert polling, deduplication. | Verified ✅ |
| [`src/models/AuditLog.php`](../src/models/AuditLog.php) | Audit trail logging for admin actions and safety overrides. | Verified ✅ |
| [`src/models/User.php`](../src/models/User.php) | User credentials, password hashing, profile updates. | Verified ✅ |

---

### B. API Endpoints

| Endpoint | Method | Header / Auth | Payload / Response | Status |
| :--- | :--- | :--- | :--- | :--- |
| [`/api/device/report.php`](../public/api/device/report.php) | `POST` | `X-API-Key` | **In:** `{soil_moisture, water_level, battery_voltage, solar_output, pump_state, schedule_id}`<br>**Out:** `{status: "ok", alerts_created: [...]}` | Operational ✅ |
| [`/api/device/pull-schedule.php`](../public/api/device/pull-schedule.php) | `GET` | `X-API-Key` | **Out:** `{schedules: [{id, label, start_time, duration_minutes, days_of_week}]}` | Operational ✅ |
| [`/api/admin/dashboard-data.php`](../public/api/admin/dashboard-data.php) | `GET` | Session Auth | **Out:** Live sensor packet, 8-day trend history, today's active schedules, recent alerts. | Operational ✅ |

---

### C. Admin Web Panel & Frontend

| View File | Functions & UI Components |
| :--- | :--- |
| [`public/admin/dashboard.php`](../public/admin/dashboard.php) | Live telemetry gauges (Moisture, Water Depth, Battery, Solar), Pump Relay status card, Quick trend chart. |
| [`public/admin/schedule.php`](../public/admin/schedule.php) | Interactive schedule creation, day-of-week toggles, duration settings, active/disabled switches. |
| [`public/admin/alerts.php`](../public/admin/alerts.php) | Real-time safety interlock and threshold alerts with acknowledge/dismiss actions. |
| [`public/admin/reports.php`](../public/admin/reports.php) | Historical telemetry queries, date range filters, CSV report export. |
| [`public/admin/logs.php`](../public/admin/logs.php) | Security and operations audit trail with user and IP tracking. |
| [`public/admin/users.php`](../public/admin/users.php) | User administration and RBAC privilege control. |

---

### D. Firmware & Arduino Controllers

| Sketch / File | Target Hardware | Responsibilities | Status |
| :--- | :--- | :--- | :--- |
| [`arduino/01_soil_root_capacitive_test.ino`](../arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino) | Arduino Uno | Root capacitive sensor test & calibration. | **Calibrated (417/153)** ✅ |
| [`arduino/02_surface_water_level_test.ino`](../arduino/02_surface_water_level_test/02_surface_water_level_test.ino) | Arduino Uno | Surface depth sensor test (HW-080). | Unit Verified ✅ |
| [`arduino/03_relay_pump_test.ino`](../arduino/03_relay_pump_test/03_relay_pump_test.ino) | Arduino Uno | Active LOW relay switching with safety pulse. | Unit Verified ✅ |
| [`arduino/04_battery_voltage_test.ino`](../arduino/04_battery_voltage_test/04_battery_voltage_test.ino) | Arduino Uno | 3S 18650 Battery divider ($100\text{k}\Omega / 33\text{k}\Omega$). | Unit Verified ✅ |
| [`arduino/05_solar_voltage_test.ino`](../arduino/05_solar_voltage_test/05_solar_voltage_test.ino) | Arduino Uno | Solar panel divider ($100\text{k}\Omega / 20\text{k}\Omega$). | Unit Verified ✅ |
| [`arduino/06_solar_charger_battery_test.ino`](../arduino/06_solar_charger_battery_test/06_solar_charger_battery_test.ino) | Arduino Uno | Combined solar charge state machine. | Unit Verified ✅ |
| [`arduino/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino) | Arduino Uno | Complete autonomous controller with dual sensors, relay interlocks, and 115200 baud serial telemetry stream. | **Calibrated (417/153)** ✅ |
| [`firmware/wbacfspwi_node/wbacfspwi_node.ino`](../firmware/wbacfspwi_node/wbacfspwi_node.ino) | ESP32 / ESP8266 | WiFi IoT node bridging hardware telemetry to web API. | Needs Battery Sync ⚠️ |

---

## 3. Pre-Integration Discrepancies & Action Items

Before linking hardware to the live web application, the following items must be verified:

1. **Battery Profile Synchronization:**
   - `config/device.php` currently has 6V SLA thresholds (`BATTERY_MIN_VOLTS = 5.4V`, `BATTERY_MAX_VOLTS = 7.2V`, `ALERT_LOW_BATTERY_VOLTS = 5.8V`).
   - If using the **3S 18650 Pack (11.1V - 12.6V)** from the Arduino miniature build:
     - `BATTERY_MIN_VOLTS` $\rightarrow$ `10.0`
     - `BATTERY_MAX_VOLTS` $\rightarrow$ `12.6`
     - `ALERT_LOW_BATTERY_VOLTS` $\rightarrow$ `10.2`
     - `ALERT_HIGH_BATTERY_VOLTS` $\rightarrow$ `13.0`
2. **WiFi Node Gateway Configuration:**
   - In `firmware/wbacfspwi_node/wbacfspwi_node.ino`, update `SSID`, `PASS`, and set `HOST` to your computer's local LAN IP (e.g. `http://192.168.1.XX/WBACFSPWI...`).
3. **Database Population:**
   - Ensure MySQL database `wbacfspwi` is running and migrated via [`database/schema.sql`](../database/schema.sql).

---

## 4. Step-by-Step Hardware-to-Software Integration Plan

```
+--------------------------+       UART Serial      +--------------------------+
|      ARDUINO UNO R3      | ---------------------> |      ESP8266 / ESP32     |
| (Dual Sensors + Relay)   |    (115200 Baud)       | (WiFi HTTP Client)       |
+--------------------------+                        +------------+-------------+
                                                                 |
                                                          HTTP POST / JSON
                                                           (X-API-Key)
                                                                 v
                                                    +--------------------------+
                                                    |    APACHE / PHP BACKEND  |
                                                    |  (/api/device/report.php)|
                                                    +------------+-------------+
                                                                 |
                                                           PDO Insert
                                                                 v
                                                    +--------------------------+
                                                    |      MYSQL DATABASE      |
                                                    |   (`sensor_readings`)    |
                                                    +------------+-------------+
                                                                 |
                                                          AJAX Poll (1-2s)
                                                                 v
                                                    +--------------------------+
                                                    |  LIVE WEB DASHBOARD      |
                                                    |  (/admin/dashboard.php)  |
                                                    +--------------------------+
```

1. **Step 1:** Flash Arduino Uno with [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino).
2. **Step 2:** Configure WiFi credentials & XAMPP server IP in the ESP node sketch.
3. **Step 3:** Perform end-to-end telemetry transmission test via `POST /api/device/report.php`.
4. **Step 4:** Verify live updates appearing on [`public/admin/dashboard.php`](../public/admin/dashboard.php).
