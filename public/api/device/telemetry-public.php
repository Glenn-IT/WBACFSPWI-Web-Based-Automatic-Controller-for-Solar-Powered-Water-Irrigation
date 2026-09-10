<?php

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

$pdo = getDb();

$totalCount = (int) $pdo->query("SELECT COUNT(*) FROM sensor_readings")->fetchColumn();
$latestStmt = $pdo->query("SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 1");
$latest = $latestStmt->fetch(PDO::FETCH_ASSOC) ?: null;

$recentStmt = $pdo->query("SELECT * FROM sensor_readings ORDER BY id DESC LIMIT 20");
$recent = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

$secondsAgo = null;
$isLive = false;

if ($latest && isset($latest['recorded_at'])) {
    $recordedTs = strtotime($latest['recorded_at']);
    $secondsAgo = max(0, time() - $recordedTs);
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
