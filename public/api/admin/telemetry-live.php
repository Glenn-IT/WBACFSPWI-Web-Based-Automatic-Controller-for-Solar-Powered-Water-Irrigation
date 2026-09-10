<?php

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

Auth::requireRole(['super_admin', 'admin', 'viewer']);

$pdo = getDb();

// Handle manual actions from test page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'clear') {
        Auth::requireRole(['super_admin', 'admin']);
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE `sensor_readings`;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        echo json_encode(['success' => true, 'message' => 'Sensor readings table purged to 0 rows.']);
        exit;
    }

    if ($action === 'inject') {
        Auth::requireRole(['super_admin', 'admin']);
        // Simulate an incoming packet from Arduino/ESP8266
        $soil = round(rand(400, 750) / 10, 1);
        $water = round(rand(200, 600) / 10, 1);
        $batt = round(rand(1220, 1340) / 100, 2);
        $solar = round(rand(1400, 1950) / 100, 2);
        $pump = $water < 45.0 ? 'on' : 'off';

        $id = SensorReading::create([
            'soil_moisture' => $soil,
            'water_level' => $water,
            'battery_voltage' => $batt,
            'solar_output' => $solar,
            'pump_state' => $pump,
        ]);

        echo json_encode([
            'success' => true,
            'message' => "Test hardware packet injected (ID #{$id})",
            'data' => [
                'id' => $id,
                'soil_moisture' => $soil,
                'water_level' => $water,
                'battery_voltage' => $batt,
                'solar_output' => $solar,
                'pump_state' => $pump,
            ]
        ]);
        exit;
    }
}

// Fetch live telemetry stats
$totalCount = (int) $pdo->query("SELECT COUNT(*) FROM sensor_readings")->fetchColumn();

$latestStmt = $pdo->query("SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 1");
$latest = $latestStmt->fetch(PDO::FETCH_ASSOC) ?: null;

$recentStmt = $pdo->query("SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 25");
$recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

$secondsAgo = null;
$isLive = false;

if ($latest && isset($latest['recorded_at'])) {
    $recordedTs = strtotime($latest['recorded_at']);
    $secondsAgo = max(0, time() - $recordedTs);
    // Considered "LIVE" if reading arrived in the last 15 seconds
    $isLive = ($secondsAgo <= 15);
}

$batteryPercent = null;
if ($latest && isset($latest['battery_voltage'])) {
    $batteryPercent = SensorReading::batteryPercent((float) $latest['battery_voltage']);
}

echo json_encode([
    'total_count' => $totalCount,
    'is_live' => $isLive,
    'seconds_ago' => $secondsAgo,
    'battery_profile' => defined('BATTERY_PROFILE') ? BATTERY_PROFILE : '12v_motorcycle',
    'battery_min' => defined('BATTERY_MIN_VOLTS') ? BATTERY_MIN_VOLTS : 10.5,
    'battery_max' => defined('BATTERY_MAX_VOLTS') ? BATTERY_MAX_VOLTS : 14.4,
    'latest' => $latest ? [
        'id' => (int) $latest['id'],
        'soil_moisture' => (float) $latest['soil_moisture'],
        'water_level' => (float) $latest['water_level'],
        'battery_voltage' => (float) $latest['battery_voltage'],
        'battery_percent' => $batteryPercent,
        'solar_output' => (float) $latest['solar_output'],
        'pump_state' => $latest['pump_state'],
        'recorded_at' => $latest['recorded_at'],
    ] : null,
    'recent' => array_map(function($r) {
        return [
            'id' => (int) $r['id'],
            'soil_moisture' => (float) $r['soil_moisture'],
            'water_level' => (float) $r['water_level'],
            'battery_voltage' => (float) $r['battery_voltage'],
            'solar_output' => (float) $r['solar_output'],
            'pump_state' => $r['pump_state'],
            'recorded_at' => $r['recorded_at'],
        ];
    }, $recent),
]);
