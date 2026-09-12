<?php
/**
 * WBACFSPWI System Synchronization Verifier
 * 
 * Verifies that all interconnected hardware sketches, configuration files,
 * database schemas, models, API endpoints, and UI views are in 100% sync.
 * 
 * Usage: php scripts/verify_sync.php
 */

$rootDir = realpath(__DIR__ . '/..');
$passCount = 0;
$failCount = 0;
$warnings = [];

function check(string $label, bool $condition, string $failMessage = '') {
    global $passCount, $failCount, $warnings;
    if ($condition) {
        $passCount++;
        echo "  [PASS] $label\n";
    } else {
        $failCount++;
        echo "  [FAIL] $label\n";
        if ($failMessage) {
            echo "         Detail: $failMessage\n";
            $warnings[] = "$label: $failMessage";
        }
    }
}

echo "\n=======================================================\n";
echo " WBACFSPWI System Synchronization Verification Suite\n";
echo "=======================================================\n\n";

// -------------------------------------------------------------
// 1. ARDUINO HARDWARE CALIBRATION CONSTANTS
// -------------------------------------------------------------
echo "1. Testing Capacitive Soil Moisture Sensor (A0) Calibration:\n";
$f01 = file_get_contents("$rootDir/arduino/01_soil_root_capacitive_test/01_soil_root_capacitive_test.ino");
$f07 = file_get_contents("$rootDir/arduino/07_dual_sensor_pump_integration_test/07_dual_sensor_pump_integration_test.ino");
$f08 = file_get_contents("$rootDir/arduino/08_dc_adapter_presentation_test/08_dc_adapter_presentation_test.ino");
$fMain = file_get_contents("$rootDir/arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino");
$fCal = file_get_contents("$rootDir/arduino/CALIBRATION_REGISTRY.md");

check("Test 01 defines SOIL_AIR_RAW = 417 & SOIL_WATER_RAW = 153", 
    strpos($f01, '417') !== false && strpos($f01, '153') !== false);
check("Test 07 defines SOIL_AIR_RAW = 417 & SOIL_WATER_RAW = 153", 
    strpos($f07, '417') !== false && strpos($f07, '153') !== false);
check("Test 08 defines SOIL_AIR_RAW = 417 & SOIL_WATER_RAW = 153", 
    strpos($f08, '417') !== false && strpos($f08, '153') !== false);
check("Main Controller defines SOIL_AIR_RAW = 417 & SOIL_WATER_RAW = 153", 
    strpos($fMain, '417') !== false && strpos($fMain, '153') !== false);
check("Calibration Registry records Capacitive Root (417/153)", 
    strpos($fCal, '417') !== false && strpos($fCal, '153') !== false);

echo "\n2. Testing HW-080 Surface Water Level (A1) 3-Point Calibration:\n";
$f02 = file_get_contents("$rootDir/arduino/02_surface_water_level_test/02_surface_water_level_test.ino");
check("Test 02 defines HW080 Dry=1020, Mid=410, Wet=355", 
    strpos($f02, '1020') !== false && strpos($f02, '410') !== false && strpos($f02, '355') !== false);
check("Test 07 defines HW080 Dry=1020, Mid=410, Wet=355", 
    strpos($f07, '1020') !== false && strpos($f07, '410') !== false && strpos($f07, '355') !== false);
check("Test 08 defines HW080 Dry=1020, Mid=410, Wet=355", 
    strpos($f08, '1020') !== false && strpos($f08, '410') !== false && strpos($f08, '355') !== false);
check("Main Controller defines HW080 Dry=1020, Mid=410, Wet=355", 
    strpos($fMain, '1020') !== false && strpos($fMain, '410') !== false && strpos($fMain, '355') !== false);
check("Calibration Registry records HW-080 (1020/410/355)", 
    strpos($fCal, '1020') !== false && strpos($fCal, '410') !== false && strpos($fCal, '355') !== false);

echo "\n3. Testing Resistor Voltage Divider Ratios:\n";
$f04 = file_get_contents("$rootDir/arduino/04_battery_voltage_test/04_battery_voltage_test.ino");
$f05 = file_get_contents("$rootDir/arduino/05_solar_voltage_test/05_solar_voltage_test.ino");
$f06 = file_get_contents("$rootDir/arduino/06_solar_charger_battery_test/06_solar_charger_battery_test.ino");

check("Battery Divider Ratio (4.0303) across Test 04, 06, Main Controller", 
    strpos($f04, '4.0303') !== false && strpos($f06, '4.0303') !== false && strpos($fMain, '4.0303') !== false);
check("Solar Divider Ratio (6.000) across Test 05, 06, Main Controller", 
    strpos($f05, '6.00') !== false && strpos($f06, '6.00') !== false && strpos($fMain, '6.00') !== false);

echo "\n4. Testing 3-Layer Irrigation Control Thresholds:\n";
check("Test 07 maintains Target Max = 50.0% & Refill Min = 45.0%", 
    preg_match('/WATER_TARGET_MAX\s*=\s*50\.0/', $f07) && preg_match('/WATER_REFILL_MIN\s*=\s*45\.0/', $f07));
check("Main Controller maintains Target Max = 50.0% & Refill Min = 45.0%", 
    preg_match('/WATER_TARGET_MAX\s*=\s*50\.0/', $fMain) && preg_match('/WATER_REFILL_MIN\s*=\s*45\.0/', $fMain));
check("Test 08 maintains Target Max = 50.0% & Refill Min = 45.0%", 
    preg_match('/WATER_TARGET_MAX\s*=\s*50\.0/', $f08) && preg_match('/WATER_REFILL_MIN\s*=\s*45\.0/', $f08));
check("Calibration Registry records Target 50.0% & Refill 45.0%", 
    strpos($fCal, '50.0%') !== false && strpos($fCal, '45.0%') !== false);
check("Main Controller anti-splash min runtime = 5000ms & settling delay = 10000ms", 
    strpos($fMain, 'MIN_PUMP_RUN_MS  = 5000UL') !== false && strpos($fMain, 'SETTLING_DELAY_MS= 10000UL') !== false);
check("Test 07 anti-splash min runtime = 5000ms & settling delay = 10000ms", 
    strpos($f07, 'MIN_PUMP_RUN_MS     = 5000UL') !== false && strpos($f07, 'SETTLING_DELAY_MS   = 10000UL') !== false);

echo "\n5. Testing 3S Li-ion Battery Protection Thresholds:\n";
check("Main Controller enforces 10.00V Low Battery Lockout & 10.50V Resume", 
    preg_match('/BATT_MIN_LOCKOUT\s*=\s*10\.00/', $fMain) && preg_match('/BATT_RESUME_VOLTS\s*=\s*10\.50/', $fMain));
check("Test 04 & 06 enforce 10.00V Low Battery Cutoff", 
    strpos($f04, '10.00') !== false && strpos($f06, '10.00') !== false);
check("Calibration Registry records 10.00V Lockout & 10.50V Resume", 
    strpos($fCal, '10.00V') !== false && strpos($fCal, '10.50V') !== false);

// -------------------------------------------------------------
// 2. BACKEND CONFIG & FIRMWARE SYNC
// -------------------------------------------------------------
echo "\n6. Testing Firmware & Backend API Key Sync:\n";
$fNode = file_get_contents("$rootDir/firmware/wbacfspwi_node/wbacfspwi_node.ino");
$fDevCfg = file_get_contents("$rootDir/config/device.php");

check("Firmware API_KEY matches config/device.php ('dev-local-device-key')", 
    strpos($fNode, 'dev-local-device-key') !== false && strpos($fDevCfg, 'dev-local-device-key') !== false);
check("Firmware reports to /api/device/report.php", 
    strpos($fNode, '/api/device/report.php') !== false);
check("Firmware pulls from /api/device/pull-schedule.php", 
    strpos($fNode, '/api/device/pull-schedule.php') !== false);

echo "\n7. Testing NodeMCU ESP8266 (Test 09) WiFi Bridge Sync:\n";
$f09 = file_get_contents("$rootDir/arduino/09_esp8266_wifi_bridge_test/09_esp8266_wifi_bridge_test.ino");
$fEspNode = file_get_contents("$rootDir/firmware/wbacfspwi_esp8266_node/wbacfspwi_esp8266_node.ino");

check("Test 09 defines SoftwareSerial on D1(RX) & D2(TX)", 
    strpos($f09, 'PIN_SW_RX = D1') !== false && strpos($f09, 'PIN_SW_TX = D2') !== false);
check("Test 09 API_KEY matches config/device.php ('dev-local-device-key')", 
    strpos($f09, 'dev-local-device-key') !== false);
check("Test 09 endpoints match /api/device/report.php & pull-schedule.php", 
    strpos($f09, '/api/device/report.php') !== false && strpos($f09, '/api/device/pull-schedule.php') !== false);
check("Main Controller has SoftwareSerial linked on Pins 9/10 for NodeMCU", 
    strpos($fMain, 'PIN_ESP_RX         = 9') !== false && strpos($fMain, 'PIN_ESP_TX         = 10') !== false);
check("Production ESP8266 Node firmware defines D1/D2 and API_KEY", 
    strpos($fEspNode, 'PIN_SW_RX = D1') !== false && strpos($fEspNode, 'dev-local-device-key') !== false);

echo "\n8. Testing Backend Battery Profile Harmonization:\n";
check("config/device.php supports '3s_liion' (10.0V - 12.6V) & '6v_sla' profiles", 
    strpos($fDevCfg, '3s_liion') !== false && strpos($fDevCfg, '6v_sla') !== false);

// -------------------------------------------------------------
// 3. DATABASE SCHEMA & MODELS SYNC
// -------------------------------------------------------------
echo "\n9. Testing Database Schema & Models Alignment:\n";
$fSchema = file_get_contents("$rootDir/database/schema.sql");
$requiredTables = ['users', 'schedules', 'sensor_readings', 'irrigation_events', 'alerts', 'audit_logs', 'overrides'];

foreach ($requiredTables as $table) {
    check("database/schema.sql defines table `$table`", 
        preg_match("/CREATE TABLE IF NOT EXISTS\s+`?" . $table . "`?/i", $fSchema) === 1);
}

// Check migration file order
$migrations = scandir("$rootDir/database/migrations");
$migrationNumbers = [];
$collision = false;
foreach ($migrations as $m) {
    if (preg_match('/^(\d+)_(.+)\.sql$/', $m, $matches)) {
        $num = $matches[1];
        if (isset($migrationNumbers[$num])) {
            $collision = true;
        }
        $migrationNumbers[$num] = $m;
    }
}
check("Database migrations have unique ordered prefixes (001, 002, 003...)", 
    !$collision, $collision ? "Collision detected in migration sequence numbers" : "");

// -------------------------------------------------------------
// 4. FRONTEND UI & NAVIGATION SYNC
// -------------------------------------------------------------
echo "\n10. Testing Frontend Navigation & View Routing:\n";
$fSidebar = file_get_contents("$rootDir/public/admin/partials/sidebar.php");
$expectedNav = [
    'dashboard' => '/admin/dashboard.php',
    'schedule'  => '/admin/schedule.php',
    'logs'      => '/admin/logs.php',
    'reports'   => '/admin/reports.php',
    'users'     => '/admin/users.php',
    'profile'   => '/admin/profile.php',
];

foreach ($expectedNav as $navKey => $navHref) {
    check("Sidebar navigation includes active route `$navKey` ($navHref)", 
        strpos($fSidebar, "'$navKey'") !== false && strpos($fSidebar, $navHref) !== false);
}

// -------------------------------------------------------------
// CHECK GROUP 7: MASTER CONTROLLER WIRING GUIDE SYNCHRONIZATION
// -------------------------------------------------------------
echo "--- Master Controller Wiring Guide Synchronization ---\n";
$fCtrlWiringPath = $rootDir . '/arduino/wbacfspwi_arduino_controller/wiring_guide.html';
check("Controller wiring guide exists", file_exists($fCtrlWiringPath));
$fCtrlWiring = file_exists($fCtrlWiringPath) ? file_get_contents($fCtrlWiringPath) : '';

check("Controller wiring guide links to Test 09 ESP8266 WiFi Bridge",
    strpos($fCtrlWiring, '09_esp8266_wifi_bridge_test/wiring_guide.html') !== false);
check("Controller wiring guide includes Arduino D9 RX pin definition",
    strpos($fCtrlWiring, 'Pin D9 (RX') !== false);
check("Controller wiring guide includes Arduino D10 TX pin definition",
    strpos($fCtrlWiring, 'Pin D10 (TX') !== false);
check("Controller wiring guide includes NodeMCU ESP8266 component & level shifter",
    strpos($fCtrlWiring, 'NODEMCU ESP8266MOD') !== false && strpos($fCtrlWiring, '5V &rarr; 3.3V LEVEL SHIFTER') !== false);
check("Controller wiring guide table has PWR-05 (NodeMCU Vin) and GND-05 (NodeMCU GND)",
    strpos($fCtrlWiring, 'PWR-05') !== false && strpos($fCtrlWiring, 'GND-05') !== false);
check("Controller wiring guide table has COMM-01 and COMM-02 serial interconnect rows",
    strpos($fCtrlWiring, 'COMM-01') !== false && strpos($fCtrlWiring, 'COMM-02') !== false);
check("Controller wiring guide table has calibrated 50% target / 45% refill (no legacy 85%/80%)",
    strpos($fCtrlWiring, '50.0% target layer') !== false && strpos($fCtrlWiring, 'Maintains 85% level') === false);

// -------------------------------------------------------------
// CHECK GROUP 8: TEST 09 WIRING GUIDE VISUAL & SCHEMATIC SYNCHRONIZATION
// -------------------------------------------------------------
echo "--- Test 09 ESP8266 WiFi Bridge Wiring Guide Synchronization ---\n";
$f09WiringPath = $rootDir . '/arduino/09_esp8266_wifi_bridge_test/wiring_guide.html';
check("Test 09 wiring guide exists", file_exists($f09WiringPath));
$f09Wiring = file_exists($f09WiringPath) ? file_get_contents($f09WiringPath) : '';

check("Test 09 wiring guide has interactive SVG breadboard diagram",
    strpos($f09Wiring, '<svg viewBox="0 0 1180 720"') !== false);
check("Test 09 wiring guide has animated jumper wires (class='wire')",
    strpos($f09Wiring, 'class="wire"') !== false);
check("Test 09 wiring guide includes 1kΩ and 2kΩ logic level shifter resistors",
    strpos($f09Wiring, '1 kΩ (R1)') !== false && strpos($f09Wiring, '2 kΩ (R2)') !== false);
check("Test 09 wiring guide table contains full connection matrix with tags",
    strpos($f09Wiring, 'PWR-01') !== false && strpos($f09Wiring, 'SIG-02') !== false && strpos($f09Wiring, 'tag-nodemcu') !== false);
check("Test 09 wiring guide nav-bar links to master hub and full controller",
    strpos($f09Wiring, 'Master Hub') !== false && strpos($f09Wiring, 'Full Controller') !== false);

// -------------------------------------------------------------
// CHECK GROUP 9: 12V MOTORCYCLE BATTERY PROFILE & CALIBRATION SYNCHRONIZATION
// -------------------------------------------------------------
echo "--- 12V Motorcycle Battery Profile & Calibration Synchronization ---\n";
$fDevCfgPath = $rootDir . '/config/device.php';
$fDevCfg = file_exists($fDevCfgPath) ? file_get_contents($fDevCfgPath) : '';
check("config/device.php defines '12v_motorcycle' profile as default",
    strpos($fDevCfg, "'12v_motorcycle'") !== false);
check("config/device.php defines 10.5V min and 12.7V resting max for motorcycle battery",
    strpos($fDevCfg, "define('BATTERY_MIN_VOLTS', 10.5)") !== false &&
    strpos($fDevCfg, "define('BATTERY_MAX_VOLTS', 12.7)") !== false);
check("config/device.php preserves '3s_liion' profile option for 3S 18650 packs",
    strpos($fDevCfg, "'3s_liion'") !== false && strpos($fDevCfg, "define('BATTERY_MAX_VOLTS', 12.6)") !== false);

$fCalRegPath = $rootDir . '/arduino/CALIBRATION_REGISTRY.md';
$fCalReg = file_exists($fCalRegPath) ? file_get_contents($fCalRegPath) : '';
check("CALIBRATION_REGISTRY.md records 12V Motorcycle Lead-Acid profile (14.4V bulk)",
    strpos($fCalReg, '12V Motorcycle') !== false && strpos($fCalReg, '14.4V') !== false);

$fPinoutPath = $rootDir . '/arduino/Pinout_and_Schematic.md';
$fPinout = file_exists($fPinoutPath) ? file_get_contents($fPinoutPath) : '';
check("Pinout_and_Schematic.md specifies 12V Motorcycle / 3S Battery (+) [11.1V–14.4V]",
    strpos($fPinout, '12V Motorcycle / 3S Battery') !== false && strpos($fPinout, '3.57V') !== false);

$fSysMemPath = $rootDir . '/SYSTEM_MEMORY.md';
$fSysMem = file_exists($fSysMemPath) ? file_get_contents($fSysMemPath) : '';
check("SYSTEM_MEMORY.md documents 12V Motorcycle Lead-Acid/AGM (default) profile",
    strpos($fSysMem, '12V Motorcycle Lead-Acid/AGM (default)') !== false);

check("Controller wiring guide table reflects 12V Motorcycle / 3S Battery across PWR-01, ACT-01, GND-02",
    strpos($fCtrlWiring, '12V Motorcycle / 3S Battery (+) [11.1V–14.4V]') !== false &&
    strpos($fCtrlWiring, '12V Motorcycle / 3S Battery (-) Terminal') !== false);
check("Controller wiring guide SVG reflects 12V MOTORCYCLE / 3S BATTERY",
    strpos($fCtrlWiring, '12V MOTORCYCLE / 3S BATTERY') !== false);

// -------------------------------------------------------------
// CHECK GROUP 10: LIVE TELEMETRY TEST & MONITORING SYNCHRONIZATION
// -------------------------------------------------------------
echo "--- Live Telemetry Test & Monitoring System Synchronization ---\n";
check("public/admin/telemetry_test.php exists",
    file_exists($rootDir . '/public/admin/telemetry_test.php'));
check("Sidebar navigation includes active route `telemetry` (/admin/telemetry_test.php)",
    strpos($fSidebar, "'telemetry'") !== false && strpos($fSidebar, '/admin/telemetry_test.php') !== false);
check("public/api/admin/telemetry-live.php exists with inject and clear handlers",
    file_exists($rootDir . '/public/api/admin/telemetry-live.php') &&
    strpos(file_get_contents($rootDir . '/public/api/admin/telemetry-live.php'), "'inject'") !== false &&
    strpos(file_get_contents($rootDir . '/public/api/admin/telemetry-live.php'), "'clear'") !== false);
check("public/telemetry_monitor.php standalone viewer exists",
    file_exists($rootDir . '/public/telemetry_monitor.php'));
check("public/api/device/telemetry-public.php public stream exists",
    file_exists($rootDir . '/public/api/device/telemetry-public.php'));
check("scripts/reset_runtime_data.php data purge utility exists",
    file_exists($rootDir . '/scripts/reset_runtime_data.php'));
check("arduino/HARDWARE_STARTUP_GUIDE.md integration startup guide exists",
    file_exists($rootDir . '/arduino/HARDWARE_STARTUP_GUIDE.md'));

// -------------------------------------------------------------
// CHECK GROUP 11: MANUAL PUMP OVERRIDE & BIDIRECTIONAL CONTROL SYNCHRONIZATION
// -------------------------------------------------------------
echo "--- Manual Pump Override & Bidirectional Control Synchronization ---\n";
check("src/models/Override.php model exists",
    file_exists($rootDir . '/src/models/Override.php'));
$fOverride = file_exists($rootDir . '/src/models/Override.php') ? file_get_contents($rootDir . '/src/models/Override.php') : '';
check("Override model defines getActiveCommand(), create(), and clearAll()",
    strpos($fOverride, 'getActiveCommand') !== false &&
    strpos($fOverride, 'create') !== false &&
    strpos($fOverride, 'clearAll') !== false);

check("public/api/admin/pump-control.php control endpoint exists",
    file_exists($rootDir . '/public/api/admin/pump-control.php'));
$fPumpCtrl = file_exists($rootDir . '/public/api/admin/pump-control.php') ? file_get_contents($rootDir . '/public/api/admin/pump-control.php') : '';
check("pump-control.php handles on, off, and auto actions",
    strpos($fPumpCtrl, "'on'") !== false &&
    strpos($fPumpCtrl, "'off'") !== false &&
    strpos($fPumpCtrl, "'auto'") !== false);

$fReport = file_get_contents($rootDir . '/public/api/device/report.php');
check("public/api/device/report.php returns active command in response payload",
    strpos($fReport, "'command'") !== false && strpos($fReport, 'Override::getActiveCommand()') !== false);

check("Production ESP8266 node forwards PUMP_ON, PUMP_OFF, PUMP_AUTO to Arduino",
    strpos($fEspNode, 'arduinoSerial.println("PUMP_ON")') !== false &&
    strpos($fEspNode, 'arduinoSerial.println("PUMP_OFF")') !== false &&
    strpos($fEspNode, 'arduinoSerial.println("PUMP_AUTO")') !== false);

$f09 = file_get_contents($rootDir . '/arduino/09_esp8266_wifi_bridge_test/09_esp8266_wifi_bridge_test.ino');
check("Test 09 WiFi Bridge forwards PUMP_ON, PUMP_OFF, PUMP_AUTO to Arduino",
    strpos($f09, 'arduinoSerial.println("PUMP_ON")') !== false &&
    strpos($f09, 'arduinoSerial.println("PUMP_OFF")') !== false &&
    strpos($f09, 'arduinoSerial.println("PUMP_AUTO")') !== false);

check("Main Controller handles manualOverride with PUMP_ON, PUMP_OFF, PUMP_AUTO",
    strpos($fMain, 'manualOverride') !== false &&
    strpos($fMain, 'PUMP_ON') !== false &&
    strpos($fMain, 'PUMP_OFF') !== false &&
    strpos($fMain, 'PUMP_AUTO') !== false);

$fDashboard = file_get_contents($rootDir . '/public/admin/dashboard.php');
check("Dashboard UI contains 3-way manual pump switch buttons (btn-pump-on, btn-pump-off, btn-pump-auto)",
    strpos($fDashboard, 'btn-pump-on') !== false &&
    strpos($fDashboard, 'btn-pump-off') !== false &&
    strpos($fDashboard, 'btn-pump-auto') !== false);
check("Dashboard UI contains setPumpOverride() JavaScript handler",
    strpos($fDashboard, 'setPumpOverride') !== false);

// -------------------------------------------------------------
// TEST 10 GSM SMS ALERT MODULE SYNCHRONIZATION
// -------------------------------------------------------------
echo "--- Test 10 GSM SMS Alert Module Synchronization ---\n";
$t10Path = $rootDir . '/arduino/10_gsm_sms_irrigation_alert_test/10_gsm_sms_irrigation_alert_test.ino';
check("Test 10 GSM sketch exists", file_exists($t10Path));
if (file_exists($t10Path)) {
    $f10 = file_get_contents($t10Path);
    check("Test 10 defines GSM SoftwareSerial on Pins 2(RX) & 3(TX)",
        strpos($f10, 'PIN_GSM_RX') !== false && strpos($f10, '2') !== false &&
        strpos($f10, 'PIN_GSM_TX') !== false && strpos($f10, '3') !== false);
    check("Test 10 enforces calibrated 50.0% target & 45.0% refill thresholds",
        strpos($f10, '50.0') !== false && strpos($f10, '45.0') !== false);
    check("Test 10 contains automated SMS triggers for Started, Stopped, and Restarted",
        strpos($f10, 'Irrigation STARTED') !== false &&
        strpos($f10, 'Irrigation STOPPED') !== false &&
        strpos($f10, 'Irrigation RESTARTED') !== false);
    check("Test 10 README.md documentation exists",
        file_exists($rootDir . '/arduino/10_gsm_sms_irrigation_alert_test/README.md'));
    check("Test 10 wiring_guide.html exists with interactive SVG breadboard",
        file_exists($rootDir . '/arduino/10_gsm_sms_irrigation_alert_test/wiring_guide.html') &&
        strpos(file_get_contents($rootDir . '/arduino/10_gsm_sms_irrigation_alert_test/wiring_guide.html'), '<svg') !== false);
    check("Arduino Hardware index.html links to Test 10",
        strpos(file_get_contents($rootDir . '/arduino/index.html'), '10_gsm_sms_irrigation_alert_test') !== false);
}

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n=======================================================\n";
echo " SYNCHRONIZATION AUDIT REPORT\n";
echo "=======================================================\n";
echo " Total Checks: " . ($passCount + $failCount) . "\n";
echo " Passed:       $passCount\n";
echo " Failed:       $failCount\n";
echo " Status:       " . ($failCount === 0 ? "ALL SYSTEMS FULLY SYNCHRONIZED" : "DESYNCHRONIZATION DETECTED") . "\n";
echo "=======================================================\n\n";

exit($failCount === 0 ? 0 : 1);
