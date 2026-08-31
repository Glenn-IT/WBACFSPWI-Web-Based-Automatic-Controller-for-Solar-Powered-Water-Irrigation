# WBACFSPWI — Sensor Calibration Registry & System Memory

This file serves as the single source of truth for physical sensor calibration values across all Arduino sketches in this project.

## Calibration Values Matrix

| Sensor / Measurement | Pin | Raw Dry / Minimum | Raw Wet / Maximum | Scaling Factor | Active Sketches |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **Capacitive Soil (Root Zone)** | `A0` | `417` (Air / 0%) | `153` (Water / 100%) | Multi-sample Avg (16x) | `01_soil_root_capacitive_test`<br>`07_dual_sensor_pump_integration_test`<br>`wbacfspwi_arduino_controller` |
| **HW-080 (Surface Ponding Level)** | `A1` | `1019` (Air / 0%) | `580` (Max Depth / 100%) | Multi-sample Avg (16x) | `02_surface_water_level_test`<br>`07_dual_sensor_pump_integration_test`<br>`wbacfspwi_arduino_controller` |
| **Battery Voltage Divider** | `A2` | `0.0V` | `12.6V` (Max 3S) | `4.0303` (100kΩ/33kΩ) | `04_battery_voltage_test`<br>`06_solar_charger_battery_test`<br>`wbacfspwi_arduino_controller` |
| **Solar Panel Voltage Divider** | `A3` | `0.0V` | `25.0V` (Max Input) | `6.0000` (100kΩ/20kΩ) | `05_solar_voltage_test`<br>`06_solar_charger_battery_test`<br>`wbacfspwi_arduino_controller` |

---

## Rice Field Irrigation Thresholds

| Parameter | Value | Behavior |
| :--- | :---: | :--- |
| **Target Surface Water Level** | `85.0%` | Pump turns **OFF** when surface ponding depth reaches $\ge 85.0\%$ |
| **Refill Surface Water Level** | `80.0%` | Pump turns **ON** when surface ponding depth drops $< 80.0\%$ |
| **Low Battery Lockout** | `10.00V` | Irrigation inhibited if battery drops $< 10.0\text{V}$ |
| **Battery Resume Voltage** | `10.50V` | Hysteresis recovery voltage before permitting irrigation |
| **Max Continuous Pump Runtime** | `180s` | Safety timeout to protect DC motor and prevent overflow |
| **Mandatory Pump Cooldown** | `60s` | Cooldown period if safety runtime cap is hit |

---

## Synchronization Rule
Whenever new physical readings are taken for either the **Capacitive Root Soil Sensor** or the **HW-080 Surface Water Sensor**:
1. Update the dedicated test sketch (`01_...` or `02_...`).
2. Update the integration test sketch (`07_...`).
3. Update the main controller (`wbacfspwi_arduino_controller.ino`).
4. Log the calibration timestamp and raw values in this file.
