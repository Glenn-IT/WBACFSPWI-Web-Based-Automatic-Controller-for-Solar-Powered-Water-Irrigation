<?php

require_once __DIR__ . '/../../../config/bootstrap.php';
header('Content-Type: application/json');

Auth::requireRole(['super_admin', 'admin']);
$user = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = strtolower(trim($_POST['action'] ?? ''));

    if ($action === 'on') {
        Override::clearAll();
        $id = Override::create('on', (int) $user['id'], 'Manual override from web dashboard');
        AuditLog::record((int) $user['id'], 'pump_manual_on', 'Turned pump ON via web dashboard');
        echo json_encode([
            'success' => true,
            'mode' => 'manual_on',
            'command' => 'PUMP_ON',
            'message' => 'Pump set to MANUAL ON. Command will dispatch to Arduino.'
        ]);
        exit;
    }

    if ($action === 'off') {
        Override::clearAll();
        $id = Override::create('off', (int) $user['id'], 'Manual override from web dashboard');
        AuditLog::record((int) $user['id'], 'pump_manual_off', 'Turned pump OFF via web dashboard');
        echo json_encode([
            'success' => true,
            'mode' => 'manual_off',
            'command' => 'PUMP_OFF',
            'message' => 'Pump set to MANUAL OFF. Command will dispatch to Arduino.'
        ]);
        exit;
    }

    if ($action === 'auto') {
        Override::clearAll();
        AuditLog::record((int) $user['id'], 'pump_mode_auto', 'Resumed automatic sensor-based control');
        echo json_encode([
            'success' => true,
            'mode' => 'auto',
            'command' => 'PUMP_AUTO',
            'message' => 'Resumed AUTOMATIC sensor control. Water level decides pump state.'
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action. Must be on, off, or auto.']);
    exit;
}

// GET request: return current override status
$latest = Override::latest();
$activeCmd = Override::getActiveCommand();

$mode = 'auto';
if ($activeCmd === 'PUMP_ON') {
    $mode = 'manual_on';
} elseif ($activeCmd === 'PUMP_OFF') {
    $mode = 'manual_off';
}

echo json_encode([
    'mode' => $mode,
    'command' => $activeCmd,
    'override' => $latest,
]);
