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
}
