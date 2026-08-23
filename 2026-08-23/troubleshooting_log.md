# WBACFSPWI — Daily Troubleshooting & Calibration Log
**Date:** August 23, 2026 (`2026-08-23`)  

---

## 🛠️ Calibration Summary
- **Capacitive Soil Sensor (v1.2):**
  - **Dry Air Reading:** `417` ADC $\rightarrow$ Set `SOIL_AIR_RAW = 417` (Maps to **`0.0%`**)
  - **Fully Submerged Reading:** `153` ADC $\rightarrow$ Set `SOIL_WATER_RAW = 153` (Maps to **`100.0%`**)
  - **Formula:** $\text{Moisture \%} = 100 \times \frac{417 - \text{Raw}}{417 - 153}$
  - **Status:** Verified and updated in [`arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino`](../arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino) and [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino).

- **HW-080 Surface Water Level Sensor:**
  - **Dry Air Reading:** `1017` ADC $\rightarrow$ Set `HW080_RAW_DRY = 1017` (Maps to **`0.0%` Surface Water**)
  - **Full Ponding Submersion Reading:** `260` ADC $\rightarrow$ Set `HW080_RAW_WET = 260` (Maps to **`100.0%` Ponding Level**; mid-level readings around `520` ADC scale smoothly to $\approx 65\%$, preventing premature flood trip).
  - **Formula:** $\text{Ponding \%} = 100 \times \frac{1017 - \text{Raw}}{1017 - 260}$
  - **Status:** Verified and updated in [`arduino/02_surface_water_level_test/02_surface_water_level_test.ino`](../arduino/02_surface_water_level_test/02_surface_water_level_test.ino) and [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino).

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
