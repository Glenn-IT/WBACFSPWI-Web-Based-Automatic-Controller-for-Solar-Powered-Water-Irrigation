<?php

class Alert
{
    public static function create(string $type, string $message): int
    {
        $stmt = getDb()->prepare('INSERT INTO alerts (type, message) VALUES (?, ?)');
        $stmt->execute([$type, $message]);
        return (int) getDb()->lastInsertId();
    }

    // Avoids spamming duplicate alerts of the same type within the given window.
    public static function existsRecent(string $type, int $withinMinutes = 30): bool
    {
        $stmt = getDb()->prepare(
            'SELECT COUNT(*) AS c FROM alerts WHERE type = ? AND created_at >= (NOW() - INTERVAL ? MINUTE)'
        );
        $stmt->execute([$type, $withinMinutes]);
        return (int) $stmt->fetch()['c'] > 0;
    }

    public static function recent(int $limit = 10): array
    {
        $limit = max(1, $limit);
        $stmt = getDb()->query("SELECT * FROM alerts ORDER BY created_at DESC LIMIT $limit");
        return $stmt->fetchAll();
    }

    // $filters may contain: type, is_read (0/1)
    public static function list(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = 'type = ?';
            $params[] = $filters['type'];
        }
        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            $where[] = 'is_read = ?';
            $params[] = (int) $filters['is_read'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $countStmt = getDb()->prepare("SELECT COUNT(*) AS total FROM alerts $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $offset = max(0, ($page - 1) * $perPage);
        $sql = "SELECT * FROM alerts $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
        $stmt = getDb()->prepare($sql);
        $stmt->execute($params);

        return [
            'rows' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    public static function countUnread(): int
    {
        $stmt = getDb()->query('SELECT COUNT(*) AS c FROM alerts WHERE is_read = 0');
        return (int) $stmt->fetch()['c'];
    }

    public static function markRead(int $id): void
    {
        $stmt = getDb()->prepare('UPDATE alerts SET is_read = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function markAllRead(): void
    {
        getDb()->exec('UPDATE alerts SET is_read = 1 WHERE is_read = 0');
    }

    public static function delete(int $id): void
    {
        $stmt = getDb()->prepare('DELETE FROM alerts WHERE id = ?');
        $stmt->execute([$id]);
    }
}
