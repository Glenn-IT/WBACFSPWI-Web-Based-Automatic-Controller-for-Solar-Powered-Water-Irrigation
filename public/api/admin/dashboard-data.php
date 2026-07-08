<?php
// AJAX endpoint polled by the admin dashboard for live widgets.

// Under-construction data gate: returns an empty payload until the Dashboard
// version is unlocked. Remove this block when v1.01 is reached.
header('Content-Type: application/json');
echo json_encode(['reading' => null, 'today_schedules' => [], 'alerts' => []]);
exit;

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

Auth::requireLogin();

$latest = SensorReading::latest();
$alerts = Alert::recent(5);

$today = strtolower(date('D'));
$todayKey = substr($today, 0, 3);
$allSchedules = array_values(array_filter(
    Schedule::all(),
    fn($s) => (int) $s['is_active'] === 1 && in_array($todayKey, explode(',', $s['days_of_week']), true)
));

echo json_encode([
    'reading' => $latest ? [
        'soil_moisture' => $latest['soil_moisture'],
        'battery_voltage' => $latest['battery_voltage'],
        'solar_output' => $latest['solar_output'],
        'pump_state' => $latest['pump_state'],
        'recorded_at' => $latest['recorded_at'],
    ] : null,
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
