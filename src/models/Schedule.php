<?php

class Schedule
{
    public static function all(): array
    {
        $stmt = getDb()->query('SELECT * FROM schedules ORDER BY start_time ASC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = getDb()->prepare('SELECT * FROM schedules WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = getDb()->prepare(
            'INSERT INTO schedules (label, start_time, duration_minutes, days_of_week, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['label'],
            $data['start_time'],
            $data['duration_minutes'],
            $data['days_of_week'],
            $data['is_active'],
            $data['created_by'],
        ]);
        return (int) getDb()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = getDb()->prepare(
            'UPDATE schedules SET label = ?, start_time = ?, duration_minutes = ?, days_of_week = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $data['label'],
            $data['start_time'],
            $data['duration_minutes'],
            $data['days_of_week'],
            $data['is_active'],
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = getDb()->prepare('DELETE FROM schedules WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function setActive(int $id, bool $isActive): void
    {
        $stmt = getDb()->prepare('UPDATE schedules SET is_active = ? WHERE id = ?');
        $stmt->execute([$isActive ? 1 : 0, $id]);
    }

    /**
     * Check if any active schedule should be running right now based on current server time.
     * Note: duration_minutes column stores seconds for presentation mode.
     */
    public static function getActiveRunningSchedule(?int $timestamp = null): ?array
    {
        $timestamp = $timestamp ?? time();
        $todayKey = strtolower(substr(date('D', $timestamp), 0, 3));
        $currentSeconds = (int) date('H', $timestamp) * 3600 + (int) date('i', $timestamp) * 60 + (int) date('s', $timestamp);

        $stmt = getDb()->query('SELECT * FROM schedules WHERE is_active = 1 ORDER BY start_time ASC');
        $schedules = $stmt->fetchAll();

        foreach ($schedules as $s) {
            $days = explode(',', strtolower($s['days_of_week']));
            if (!in_array($todayKey, $days, true)) {
                continue;
            }

            // Parse start_time (HH:MM or HH:MM:SS)
            $parts = explode(':', $s['start_time']);
            $startH = (int) ($parts[0] ?? 0);
            $startM = (int) ($parts[1] ?? 0);
            $startS = (int) ($parts[2] ?? 0);
            $startSec = $startH * 3600 + $startM * 60 + $startS;

            // In presentation mode, duration_minutes is interpreted as seconds
            $durationSec = max(1, (int) $s['duration_minutes']);
            $endSec = $startSec + $durationSec;

            if ($currentSeconds >= $startSec && $currentSeconds < $endSec) {
                $s['duration_seconds'] = $durationSec;
                $s['remaining_seconds'] = $endSec - $currentSeconds;
                return $s;
            }
        }

        return null;
    }

    /**
     * Trigger an instant presentation test run of a schedule for its configured duration in seconds.
     */
    public static function triggerTestRun(int $id, int $userId): array
    {
        $sched = self::find($id);
        if (!$sched) {
            throw new InvalidArgumentException("Schedule #$id not found.");
        }

        $durationSec = max(1, (int) $sched['duration_minutes']);
        Override::clearAll();
        $reason = "Presentation Test Run: {$sched['label']} [test_run_seconds:{$durationSec}]";
        $overrideId = Override::create('on', $userId, $reason);

        AuditLog::record($userId, 'schedule_test_run', "Triggered {$durationSec}s test run for schedule #$id ({$sched['label']})");

        return [
            'success' => true,
            'schedule' => $sched,
            'duration_seconds' => $durationSec,
            'override_id' => $overrideId,
            'message' => "Schedule '{$sched['label']}' test run active for {$durationSec} seconds.",
        ];
    }
}
