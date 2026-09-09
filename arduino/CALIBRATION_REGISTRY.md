# WBACFSPWI — Sensor Calibration Registry & System Memory

This file serves as the single source of truth for physical sensor calibration values across all Arduino sketches in this project.

## Calibration Values Matrix

| Sensor / Measurement | Pin | Raw Dry / Minimum | Raw Wet / Maximum | Scaling Factor | Active Sketches |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **Capacitive Soil (Root Zone)** | `A0` | `417` (Air / 0%) | `153` (Water / 100%) | Multi-sample Avg (16x) | `01_soil_root_capacitive_test`<br>`07_dual_sensor_pump_integration_test`<br>`wbacfspwi_arduino_controller` |
| **HW-080 (Surface Ponding Level)** | `A1` | `1020` (Air / 0%)<br>`410` (Mid / 50%) | `355` (Full / 100%) | 3-Point Physical Ruler Calibration | `02_surface_water_level_test`<br>`07_dual_sensor_pump_integration_test`<br>`wbacfspwi_arduino_controller` |
| **Battery Voltage Divider** | `A2` | `0.0V` | `12.6V` (Max 3S) / `14.4V` (Motorcycle) | `4.0303` (100kΩ/33kΩ) | `04_battery_voltage_test`<br>`06_solar_charger_battery_test`<br>`wbacfspwi_arduino_controller` |
| **Solar Panel Voltage Divider** | `A3` | `0.0V` | `25.0V` (Max Input) | `6.0000` (100kΩ/20kΩ) | `05_solar_voltage_test`<br>`06_solar_charger_battery_test`<br>`wbacfspwi_arduino_controller` |

---

## Rice Field Irrigation Thresholds

| Parameter | Value | Behavior |
| :--- | :---: | :--- |
| **Target Surface Water Level (Max)** | `50.0%` | Pump turns **OFF** when surface ponding depth reaches $\ge 50.0\%$ |
| **Refill Surface Water Level (Min)** | `45.0%` | Pump turns **ON** when surface ponding depth drops $< 45.0\%$ (5% Hysteresis Gap) |
| **Anti-Splash Minimum Runtime** | `5s` (`5000ms`) | Minimum pump runtime to prevent immediate wave/splash shutoff |
| **Wave Settling Stabilization Window** | `10s` (`10000ms`) | Stabilization delay before confirming stable water level |
| **Low Battery Lockout** | `10.00V` | Irrigation inhibited if battery drops $< 10.00\text{V}$ (deep discharge protection for 3S Li-ion & 12V Motorcycle) |
| **Battery Resume Voltage** | `10.50V` | Hysteresis recovery voltage before permitting irrigation resumption |
| **Max Continuous Pump Runtime** | `180s` | Safety timeout to protect DC motor and prevent overflow |
| **Mandatory Pump Cooldown** | `60s` | Cooldown period if safety runtime cap is hit |

---

## Battery Chemistry & Profile Reference

| Battery Type | Nominal | Low Lockout / Cutoff | Fully Charged (Resting) | Solar Bulk Charge | Solar Charger Setting |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **12V Motorcycle Lead-Acid / AGM** | `12.0V` | `10.00V` &ndash; `10.80V` | `12.60V` &ndash; `12.80V` | `14.20V` &ndash; `14.40V` | `b01` (Lead-Acid / SLA) |
| **3S 18650 Li-ion Battery Pack** | `11.1V` | `10.00V` | `12.60V` | `12.60V` (CC/CV) | `b02` (3S Li-ion BMS) |
| **6V 4.5Ah Sealed Lead-Acid** | `6.0V` | `5.40V` | `6.30V` &ndash; `6.45V` | `7.20V` | Direct trickle / No controller |

---

## Physical Calibration History Log

| Date | Sensor | Dry Raw ADC | Wet / Full Raw ADC | Notes |
| :--- | :--- | :---: | :---: | :--- |
| Initial | Capacitive Soil (A0) | `417` | `153` | Air (0%) vs Full water submersion (100%) |
| 2026-08-31 | HW-080 Surface (A1) | `1020` (Air)<br>`410` (Middle 50%) | `355` (Full 100%) | 3-Point physical ruler calibration: 1020=0% (dry air), 410=50.0% (exact middle/7-8cm), 355=100.0% (top header / max flood). |
| 2026-09-09 | 12V Battery Divider (A2) | `0.0V` | `14.4V` | Verified compatible with 12V Motorcycle Lead-Acid (up to 14.4V bulk solar charge = 3.57V on ADC). |

---

## Synchronization Rule
Whenever new physical readings are taken for either the **Capacitive Root Soil Sensor** or the **HW-080 Surface Water Sensor**:
1. Update the dedicated test sketch (`01_...` or `02_...`).
2. Update the integration test sketch (`07_...`).
3. Update the main controller (`wbacfspwi_arduino_controller.ino`).
4. Log the calibration timestamp and raw values in this file.
