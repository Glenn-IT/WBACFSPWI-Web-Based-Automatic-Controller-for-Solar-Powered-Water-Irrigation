<?php
// Device (ESP8266/ESP32) posts sensor readings + pump state here periodically.
// Expects JSON body: { "soil_moisture": 45.2, "water_level": 60.0, "battery_voltage": 12.1, "solar_output": 30.5,
//                       "pump_state": "on"|"off", "schedule_id": 3 (optional, only when trigger is scheduled) }

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

DeviceAuth::requireValidKey();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);

if (!is_array($payload) || !isset($payload['pump_state'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$pumpState = $payload['pump_state'] === 'on' ? 'on' : 'off';
$soilMoisture = isset($payload['soil_moisture']) ? (float) $payload['soil_moisture'] : null;
$waterLevel = isset($payload['water_level']) ? (float) $payload['water_level'] : null;
$batteryVoltage = isset($payload['battery_voltage']) ? (float) $payload['battery_voltage'] : null;
$solarOutput = isset($payload['solar_output']) ? (float) $payload['solar_output'] : null;
$scheduleId = isset($payload['schedule_id']) ? (int) $payload['schedule_id'] : null;

SensorReading::create([
    'soil_moisture' => $soilMoisture,
    'water_level' => $waterLevel,
    'battery_voltage' => $batteryVoltage,
    'solar_output' => $solarOutput,
    'pump_state' => $pumpState,
]);

// Track irrigation event lifecycle based on pump state transitions.
$running = IrrigationEvent::findRunning();
if ($pumpState === 'on' && !$running) {
    IrrigationEvent::start($scheduleId ? 'scheduled' : 'manual', $scheduleId);
} elseif ($pumpState === 'off' && $running) {
    IrrigationEvent::complete((int) $running['id']);
}

// Evaluate alert thresholds (throttled to avoid duplicate spam within 30 minutes).
$newAlerts = [];

if ($soilMoisture !== null && $soilMoisture < ALERT_LOW_MOISTURE_PCT && !Alert::existsRecent('low_moisture')) {
    Alert::create('low_moisture', "Soil moisture low: {$soilMoisture}%");
    $newAlerts[] = 'low_moisture';
}

if ($batteryVoltage !== null && $batteryVoltage < ALERT_LOW_BATTERY_VOLTS && !Alert::existsRecent('low_battery')) {
    Alert::create('low_battery', "Battery voltage low: {$batteryVoltage}V");
    $newAlerts[] = 'low_battery';
}

echo json_encode(['status' => 'ok', 'alerts_created' => $newAlerts]);
