<?php
// Device (ESP8266/ESP32) polls this on boot and periodically to sync active
// schedules and any pending manual override.

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

// Latest override is only considered "pending" while inside its auto-revert window
// (defaults to 60 minutes if the override didn't specify one).
$override = null;
$stmt = getDb()->query('SELECT * FROM overrides ORDER BY created_at DESC LIMIT 1');
$latestOverride = $stmt->fetch();

if ($latestOverride) {
    $minutes = (int) ($latestOverride['auto_revert_minutes'] ?? 60);
    $stmt2 = getDb()->prepare('SELECT NOW() <= (? + INTERVAL ? MINUTE) AS still_active');
    $stmt2->execute([$latestOverride['created_at'], $minutes]);
    $stillActive = (bool) $stmt2->fetch()['still_active'];

    if ($stillActive) {
        $override = [
            'action' => $latestOverride['action'],
            'reason' => $latestOverride['reason'],
            'created_at' => $latestOverride['created_at'],
            'auto_revert_minutes' => $minutes,
        ];
    }
}

echo json_encode([
    'schedules' => $schedules,
    'override' => $override,
]);
