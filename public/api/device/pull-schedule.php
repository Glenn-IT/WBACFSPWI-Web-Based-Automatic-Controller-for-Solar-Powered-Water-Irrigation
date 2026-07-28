<?php
// Device (ESP8266/ESP32) polls this on boot and periodically to sync active schedules.

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

DeviceAuth::requireValidKey();

$schedules = array_values(array_filter(Schedule::all(), fn($s) => (int) $s['is_active'] === 1));
$schedules = array_map(function ($s) {
    return [
        'id' => (int) $s['id'],
        'label' => $s['label'],
        'start_time' => substr($s['start_time'], 0, 5),
        'duration_minutes' => (int) $s['duration_minutes'],
        'days_of_week' => explode(',', $s['days_of_week']),
    ];
}, $schedules);

echo json_encode([
    'schedules' => $schedules,
]);
