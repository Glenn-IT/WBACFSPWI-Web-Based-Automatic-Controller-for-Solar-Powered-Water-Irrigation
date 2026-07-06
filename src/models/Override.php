<?php

class Override
{
    public static function create(int $userId, string $action, ?string $reason, int $autoRevertMinutes): int
    {
        $stmt = getDb()->prepare(
            'INSERT INTO overrides (user_id, action, reason, auto_revert_minutes) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $action, $reason, $autoRevertMinutes]);
        return (int) getDb()->lastInsertId();
    }

    // Returns the latest override plus whether it's still inside its auto-revert window.
    public static function latest(): ?array
    {
        $stmt = getDb()->query(
            'SELECT o.*, u.name AS user_name,
                    (NOW() <= (o.created_at + INTERVAL o.auto_revert_minutes MINUTE)) AS still_active
             FROM overrides o
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT 1'
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function recent(int $limit = 20): array
    {
        $limit = max(1, $limit);
        $stmt = getDb()->query(
            "SELECT o.*, u.name AS user_name FROM overrides o
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT $limit"
        );
        return $stmt->fetchAll();
    }
}
