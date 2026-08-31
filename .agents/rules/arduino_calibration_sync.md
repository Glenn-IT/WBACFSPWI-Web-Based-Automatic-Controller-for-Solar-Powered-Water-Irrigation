# System Rule: Arduino Calibration Synchronization Protocol

## Mandate
Whenever any sensor calibration constant, voltage divider factor, or control threshold is updated or recalibrated in any individual test sketch under `arduino/0x_*`, the exact same calibrated values **MUST IMMEDIATELY BE SYNCHRONIZED** with the main production controller:
`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`

## Tracked Calibration Constants

### 1. Capacitive Soil Moisture Sensor (Root Zone - Pin A0)
- `SOIL_AIR_RAW`: Raw 10-bit ADC in dry air (0% moisture)
- `SOIL_WATER_RAW`: Raw 10-bit ADC fully submerged in water (100% moisture)
- Applicable files:
  - `arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino`
  - `arduino/07_dual_sensor_pump_integration_test/07_dual_sensor_pump_integration_test.ino`
  - `arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`

### 2. HW-080 Surface Moisture / Ponding Level Sensor (Pin A1)
- `HW080_RAW_DRY`: Raw 10-bit ADC in dry air (0% standing water)
- `HW080_RAW_WET`: Raw 10-bit ADC at maximum container depth / full submergence (100% standing water)
- Applicable files:
  - `arduino/02_surface_water_level_test/02_surface_water_level_test.ino`
  - `arduino/07_dual_sensor_pump_integration_test/07_dual_sensor_pump_integration_test.ino`
  - `arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`

### 3. Voltage Divider Ratios
- `VBATT_RATIO` / `BATT_DIVIDER`: (100kΩ + 33kΩ) / 33kΩ = `4.0303` (Pin A2)
- `VSOLAR_RATIO` / `SOLAR_DIVIDER`: (100kΩ + 20kΩ) / 20kΩ = `6.0000` (Pin A3)
- Applicable files:
  - `arduino/04_battery_voltage_test/04_battery_voltage_test.ino`
  - `arduino/05_solar_voltage_test/05_solar_voltage_test.ino`
  - `arduino/06_solar_charger_battery_test/06_solar_charger_battery_test.ino`
  - `arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`

### 4. Irrigation Control Thresholds
- `WATER_TARGET_MAX` (e.g., 85.0%): Pump OFF threshold
- `WATER_REFILL_MIN` (e.g., 80.0%): Pump ON threshold
- Must remain synchronized between Test 07 and `wbacfspwi_arduino_controller.ino`.
