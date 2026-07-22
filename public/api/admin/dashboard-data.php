<?php
// AJAX endpoint polled by the admin dashboard for live widgets.

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

Auth::requireLogin();

$latest = SensorReading::latest();
$isSample = $latest === null;
$reading = $latest ?? SensorReading::sample();

$history = $isSample ? SensorReading::sampleHistory(8) : SensorReading::dailyTrend(8);

$alerts = Alert::recent(5);

$today = strtolower(date('D'));
$todayKey = substr($today, 0, 3);
$allSchedules = array_values(array_filter(
    Schedule::all(),
    fn($s) => (int) $s['is_active'] === 1 && in_array($todayKey, explode(',', $s['days_of_week']), true)
));

echo json_encode([
    'is_sample' => $isSample,
    'reading' => [
        'soil_moisture' => $reading['soil_moisture'],
        'water_level' => $reading['water_level'],
        'battery_percent' => SensorReading::batteryPercent($reading['battery_voltage'] !== null ? (float) $reading['battery_voltage'] : null),
        'solar_output' => $reading['solar_output'],
        'pump_state' => $reading['pump_state'],
        'recorded_at' => $reading['recorded_at'],
    ],
    'trend' => [
        'labels' => array_map(fn($r) => substr($r['recorded_at'], 0, 10), $history),
        'soil_moisture' => array_map(fn($r) => $r['soil_moisture'], $history),
        'water_level' => array_map(fn($r) => $r['water_level'], $history),
        'battery_percent' => array_map(fn($r) => SensorReading::batteryPercent($r['battery_voltage'] !== null ? (float) $r['battery_voltage'] : null), $history),
    ],
    'today_schedules' => array_map(fn($s) => [
        'label' => $s['label'],
        'start_time' => substr($s['start_time'], 0, 5),
        'duration_minutes' => (int) $s['duration_minutes'],
    ], $allSchedules),
    'alerts' => array_map(fn($a) => [
        'type' => $a['type'],
        'message' => $a['message'],
        'created_at' => $a['created_at'],
    ], $alerts),
]);
