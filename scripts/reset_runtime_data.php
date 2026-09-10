<?php

require_once __DIR__ . '/../config/database.php';

echo "=======================================================\n";
echo " WBACFSPWI Runtime Data Purge & Reset Utility\n";
echo "=======================================================\n\n";

try {
    $pdo = getDb();
} catch (Exception $e) {
    echo "[ERROR] Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

$tablesToClear = [
    'sensor_readings',
    'irrigation_events',
    'alerts',
    'audit_logs',
    'overrides'
];

echo "1. Current Row Counts Before Purge:\n";
foreach ($tablesToClear as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        echo sprintf("   %-20s : %d rows\n", $table, $count);
    } catch (Exception $e) {
        echo sprintf("   %-20s : [Table not found or error]\n", $table);
    }
}

echo "\n2. Purging Runtime Data...\n";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
foreach ($tablesToClear as $table) {
    try {
        $pdo->exec("TRUNCATE TABLE `{$table}`;");
        echo "   [CLEARED] `{$table}` truncated (AUTO_INCREMENT reset to 1)\n";
    } catch (Exception $e) {
        echo "   [WARN] Could not truncate `{$table}`: " . $e->getMessage() . "\n";
    }
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "\n3. Verifying Zero Counts Post-Purge:\n";
$allZero = true;
foreach ($tablesToClear as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        echo sprintf("   %-20s : %d rows\n", $table, $count);
        if ($count > 0) $allZero = false;
    } catch (Exception $e) {
        echo sprintf("   %-20s : [Check failed]\n", $table);
    }
}

echo "\n4. Preserved Core Configuration Tables:\n";
foreach (['users', 'schedules'] as $table) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        echo sprintf("   %-20s : %d rows (preserved)\n", $table, $count);
    } catch (Exception $e) {
        echo sprintf("   %-20s : [Error checking]\n", $table);
    }
}

echo "\n=======================================================\n";
if ($allZero) {
    echo " SUCCESS: Database is clean (0 runtime data)!\n";
    echo " Ready for fresh Arduino <-> Web integration testing.\n";
} else {
    echo " WARNING: Some tables could not be completely emptied.\n";
}
echo "=======================================================\n";
