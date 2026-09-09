# Agent Rule: Whole System Synchronization Protocol

## Mandate
Whenever any code, configuration, calibration, schema, or route is created, updated, or refactored in this repository, the agent **MUST** review the connected dependency chain defined in `SYSTEM_MEMORY.md` and propagate the changes to **ALL** connected files.

## Synchronization Triggers & Targets

1. **Hardware Calibration & Thresholds:**
   - Source: `arduino/01_*`, `arduino/02_*`, `arduino/04_*`, `arduino/05_*`, `arduino/06_*`
   - Linked Targets:
     - `arduino/07_dual_sensor_pump_integration_test/07_dual_sensor_pump_integration_test.ino`
     - `arduino/08_dc_adapter_presentation_test/08_dc_adapter_presentation_test.ino`
     - `arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`
     - `arduino/wbacfspwi_arduino_controller/wiring_guide.html`
     - `arduino/CALIBRATION_REGISTRY.md`
     - `SYSTEM_MEMORY.md`

2. **API Keys & Device Auth:**
   - Source: `config/device.php` (`DEVICE_API_KEY`)
   - Linked Targets:
     - `firmware/wbacfspwi_node/wbacfspwi_node.ino` (`API_KEY`)
     - `src/helpers/DeviceAuth.php`
     - Documentation

3. **Battery Profiles & Alert Limits:**
   - Source: `config/device.php`
   - Linked Targets:
     - `src/models/SensorReading.php` (`batteryPercent()`)
     - `public/api/device/report.php`
     - `firmware/wbacfspwi_node/wbacfspwi_node.ino`

4. **Database Tables, Migrations & Models:**
   - Source: `database/schema.sql`
   - Linked Targets:
     - `database/migrations/` (Sequential 3-digit prefixes, no collisions)
     - `src/models/*.php`
     - Corresponding API endpoints (`report.php`, `dashboard-data.php`, `pull-schedule.php`)

5. **Admin Portal Routes & Views:**
   - Source: New or modified admin page in `public/admin/<page>.php`
   - Linked Targets:
     - `public/admin/partials/sidebar.php` (`$navItems`)
     - `$activePage` variable on the page matching `$navItems` key
     - Permission guard checking role (`super_admin`, `admin`, `viewer`)

## Mandatory Post-Edit Verification
Before concluding any task involving changes to hardware sketches, configs, database, or UI, run:
```bash
php scripts/verify_sync.php
```
Ensure all checks pass with 0 failures.
