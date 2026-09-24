<?php

class Override
{
    public static function create(string $action, ?int $userId = null, ?string $reason = null, ?int $autoRevertMinutes = null): int
    {
        $stmt = getDb()->prepare(
            'INSERT INTO overrides (user_id, action, reason, auto_revert_minutes) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $action, $reason, $autoRevertMinutes]);
        return (int) getDb()->lastInsertId();
    }

    public static function latest(): ?array
    {
        $stmt = getDb()->query('SELECT * FROM overrides ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function clearAll(): void
    {
        getDb()->exec('DELETE FROM overrides');
    }

    // Returns active command: 'PUMP_ON', 'PUMP_OFF', or 'PUMP_AUTO'
    public static function getActiveCommand(): string
    {
        $latest = self::latest();
        if (!$latest) {
            return 'PUMP_AUTO';
        }

        // Check if test_run_seconds is specified in reason (e.g. "test_run_seconds:15")
        if (!empty($latest['reason']) && preg_match('/test_run_seconds:(\d+)/', $latest['reason'], $m)) {
            $testRunSec = (int) $m[1];
            $createdTs = strtotime($latest['created_at']);
            if (time() - $createdTs >= $testRunSec) {
                self::clearAll();
                return 'PUMP_AUTO';
            }
        }

        // Check if auto_revert_minutes has expired
        if (!empty($latest['auto_revert_minutes'])) {
            $createdTs = strtotime($latest['created_at']);
            if (time() - $createdTs > ($latest['auto_revert_minutes'] * 60)) {
                self::clearAll();
                return 'PUMP_AUTO';
            }
        }

        return $latest['action'] === 'on' ? 'PUMP_ON' : 'PUMP_OFF';
    }
}
