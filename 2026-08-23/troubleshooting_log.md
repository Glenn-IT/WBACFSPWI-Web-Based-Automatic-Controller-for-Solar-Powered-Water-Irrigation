# WBACFSPWI — Daily Troubleshooting & Calibration Log
**Date:** August 23, 2026 (`2026-08-23`)  

---

## 🛠️ Calibration Summary
- **Capacitive Soil Sensor (v1.2):**
  - **Dry Air Reading:** `417` ADC $\rightarrow$ Set `SOIL_AIR_RAW = 417` (Maps to **`0.0%`**)
  - **Fully Submerged Reading:** `153` ADC $\rightarrow$ Set `SOIL_WATER_RAW = 153` (Maps to **`100.0%`**)
  - **Formula:** $\text{Moisture \%} = 100 \times \frac{417 - \text{Raw}}{417 - 153}$
  - **Status:** Verified and updated in [`arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino`](../arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino) and [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino).

- **HW-080 Surface Water Level Sensor (5-Stage Water Rise Calibration from `note.md`):**
  - **Stage A (Dry Air):** `1019` ADC ($4.98\text{V}$) $\rightarrow$ Set `HW080_RAW_DRY = 1019` (Maps to **`0.0%` Surface Dry**)
  - **Stage B (First Contact / Low Rise):** `660` ADC ($3.23\text{V}$) $\rightarrow$ Maps to **`70.2%`** (OPTIMAL RICE PONDING DEPTH)
  - **Stage C (Mid Rise):** `581` ADC ($2.84\text{V}$) $\rightarrow$ Maps to **`85.7%`** (OPTIMAL RICE PONDING DEPTH)
  - **Stage D (High Rise):** `525` ADC ($2.57\text{V}$) $\rightarrow$ Maps to **`96.6%`**
  - **Stage E (Full Depth / 2-Pin Header Top):** `508` ADC ($2.48\text{V}$) $\rightarrow$ Set `HW080_RAW_WET = 508` (Maps to **`100.0%` MAX FLOOD LEVEL**)
  - **Formula:** $\text{Ponding \%} = 100 \times \frac{1019 - \text{Raw}}{1019 - 508}$
  - **Status:** Verified and updated in [`arduino/02_surface_water_level_test/02_surface_water_level_test.ino`](../arduino/02_surface_water_level_test/02_surface_water_level_test.ino) and [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino).

- **Dual Sensor & Relay Pump Integrated Rules (Test 07):**
  - **Capacitive Soil Sensor (Pin A0):** Measures Root-Zone Moisture ($0\text{--}100\%$).
  - **HW-080 Water Level Sensor (Pin A1):** Measures Surface Standing Water / Ponding Depth ($0\text{--}100\%$).
  - **Pump ON Trigger:** Root Soil Moisture $< 50.0\%$ **AND** Surface Water Level $< 70.0\%$.
  - **Pump OFF Cutoff 1 (Flood Prevention):** Surface Water Level $\ge 70.0\%$ (Immediate cutoff regardless of root dryness).
  - **Pump OFF Cutoff 2 (Target Satisfied):** Root Soil Moisture $\ge 70.0\%$.
  - **Safety Limit:** 180 seconds maximum continuous runtime protection.
  - **Created Test Sketch:** [`arduino/07_dual_sensor_pump_integration_test/07_dual_sensor_pump_integration_test.ino`](../arduino/07_dual_sensor_pump_integration_test/07_dual_sensor_pump_integration_test.ino).

---

## 🔍 Pre-Integration System Status
- **Backend APIs:**
  - `POST /api/device/report.php` $\rightarrow$ Ready for IoT node payload.
  - `GET /api/device/pull-schedule.php` $\rightarrow$ Ready to stream active irrigation schedules to hardware.
  - `GET /api/admin/dashboard-data.php` $\rightarrow$ Polling real database records with automatic sample fallback.
- **Database:** `wbacfspwi` MySQL schema complete with `sensor_readings`, `irrigation_events`, `schedules`, `alerts`, `audit_logs`, and `users`.
- **Frontend Dashboard:** [`public/admin/dashboard.php`](../public/admin/dashboard.php) ready to display live telemetry.

---

## 📋 Next Troubleshooting & Verification Steps
1. Test HW-080 Surface Water Level Sensor (Test 02) values in air vs water tray.
2. Test Relay switching & DC pump response (Test 03) under 12V battery power.
3. Verify Battery voltage divider (Test 04) reading on Pin A2 against digital multimeter.
4. Verify Solar panel voltage divider (Test 05) reading on Pin A3 under sunlight/lamp.
5. Connect ESP WiFi node and test live `POST` into XAMPP MySQL database.
