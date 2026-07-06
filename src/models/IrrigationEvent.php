<?php

class IrrigationEvent
{
    // Returns the currently running event (no ended_at yet), if any.
    public static function findRunning(): ?array
    {
        $stmt = getDb()->query(
            "SELECT * FROM irrigation_events WHERE status = 'running' ORDER BY started_at DESC LIMIT 1"
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function start(string $triggerType, ?int $scheduleId = null): int
    {
        $stmt = getDb()->prepare(
            "INSERT INTO irrigation_events (schedule_id, trigger_type, started_at, status)
             VALUES (?, ?, NOW(), 'running')"
        );
        $stmt->execute([$scheduleId, $triggerType]);
        return (int) getDb()->lastInsertId();
    }

    public static function complete(int $id): void
    {
        $stmt = getDb()->prepare(
            "UPDATE irrigation_events SET ended_at = NOW(), status = 'completed' WHERE id = ?"
        );
        $stmt->execute([$id]);
    }

    public static function recent(int $limit = 20): array
    {
        $limit = max(1, $limit);
        $stmt = getDb()->query(
            "SELECT * FROM irrigation_events ORDER BY started_at DESC LIMIT $limit"
        );
        return $stmt->fetchAll();
    }

    // $from/$to are 'Y-m-d' date strings (inclusive).
    public static function between(string $from, string $to): array
    {
        $stmt = getDb()->prepare(
            'SELECT e.*, s.label AS schedule_label FROM irrigation_events e
             LEFT JOIN schedules s ON s.id = e.schedule_id
             WHERE e.started_at >= ? AND e.started_at <= ?
             ORDER BY e.started_at DESC'
        );
        $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
        return $stmt->fetchAll();
    }
}
