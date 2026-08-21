# WBACFSPWI — Arduino Hardware System Guide

Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)
This directory contains the standalone Arduino Uno hardware sketches, component test suites, calibration utilities, circuit diagrams, and full automated rice field irrigation logic.

---

## 1. Hardware Architecture

```
[30W Solar Panel (18V-21V)]
            │
            ▼
[Solar Charger Module / 3S BMS]
            │
            ▼
[3x 18650 Li-ion Battery Pack (3S: 11.1V - 12.6V)] ───────────────┐
            │                                                      │
            ▼                                                      ▼
[Buck Converter (Step-Down LM2596)]                        [Relay Module Switch]
            │ (Regulated 5.0V output)                              │
            ▼                                                      ▼
[Arduino Uno R3 Power Rails (5V / GND)]                    [DC Water Pump (Submersible)]
   ├── A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone)        │ (Waters Rice Field)
   ├── A1: HW-080 Moisture Sensor (Surface Water / Ponding)────────┘
   ├── A2: Battery Voltage Divider Tap (100kΩ / 33kΩ)
   ├── A3: Solar Voltage Divider Tap (100kΩ / 20kΩ)
   ├── D7: 5V Relay Control Signal (Active LOW/HIGH)
   ├── D8: Sensor Power Gate (Optional corrosion prevention)
   └── D13: Status / Fault LED
```

---

## 2. Directory & Sketch Index

| Folder / File | Purpose | Description |
|---|---|---|
| [`Pinout_and_Schematic.md`](./Pinout_and_Schematic.md) | Wiring Reference | Complete pin assignments, voltage divider math, wiring table, and electrical safety guidelines. |
| [`01_soil_root_capacitive_test/`](./01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino) | Root Sensor Test | Calibrates Capacitive Moisture Sensor v1.2 in dry air vs. water to determine ADC thresholds. |
| [`02_surface_water_level_test/`](./02_surface_water_level_test/02_surface_water_level_test.ino) | Surface Sensor Test | Calibrates HW-080 Moisture Sensor module (probe + LM393 board) for detecting standing water in rice field. |
| [`03_relay_pump_test/`](./03_relay_pump_test/03_relay_pump_test.ino) | Relay & Pump Test | Safe pulse testing of relay module and DC water pump with runtime protections. |
| [`04_battery_voltage_test/`](./04_battery_voltage_test/04_battery_voltage_test.ino) | Battery Monitor Test | Reads and calibrates 3S 18650 pack voltage (11.1V - 12.6V) via resistor voltage divider. |
| [`05_solar_voltage_test/`](./05_solar_voltage_test/05_solar_voltage_test.ino) | Solar Monitor Test | Reads and calibrates 30W solar panel open circuit / operating voltage via voltage divider. |
| [`wbacfspwi_arduino_controller/`](./wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino) | **Full Controller** | Complete automated controller featuring dual-sensor logic, safety cutoffs, relay hysteresis, and serial telemetry. |

---

## 3. Quick Start & Assembly Steps

### Step 1: Power & Buck Converter Preparation (CRITICAL)
1. Connect the 3S 18650 battery pack ($11.1\text{V}-12.6\text{V}$) to the **IN+ / IN-** terminals of the Buck Converter.
2. **DO NOT connect Arduino yet.**
3. Turn on power, place a digital multimeter on the **OUT+ / OUT-** terminals of the Buck Converter.
4. Turn the small potentiometer brass screw until the multimeter reads exactly **5.00V** (or 7.0V if powering via Arduino VIN pin).
5. Once verified at 5.0V, connect Buck OUT+ to Arduino **5V** (or 7V to **VIN**) and Buck OUT- to Arduino **GND**.

### Step 2: Individual Component Calibration
1. Upload and run `01_soil_root_capacitive_test.ino`. Note down your `SOIL_AIR_RAW` (dry) and `SOIL_WATER_RAW` (wet) values.
2. Upload and run `02_surface_water_level_test.ino`. Calibrate surface standing water threshold.
3. Upload and run `04_battery_voltage_test.ino` and `05_solar_voltage_test.ino` to verify voltage divider readings against your multimeter.

### Step 3: Flash the Integrated Controller
1. Open `wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`.
2. Update any calibration constants if your measured values differed slightly.
3. Upload to Arduino Uno and open Serial Monitor at **115200 baud** to observe automated irrigation operations and real-time telemetry.
