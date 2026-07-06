<?php

class AuditLog
{
    public static function record(?int $userId, string $action, string $details = ''): void
    {
        $stmt = getDb()->prepare(
            'INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
    }

    // $filters may contain: user_id, action, date_from, date_to
    public static function list(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = 'a.user_id = ?';
            $params[] = $filters['user_id'];
        }
        if (!empty($filters['action'])) {
            $where[] = 'a.action LIKE ?';
            $params[] = '%' . $filters['action'] . '%';
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'a.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'a.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $countStmt = getDb()->prepare("SELECT COUNT(*) AS total FROM audit_logs a $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $offset = max(0, ($page - 1) * $perPage);
        $sql = "SELECT a.*, u.name AS user_name FROM audit_logs a
                LEFT JOIN users u ON u.id = a.user_id
                $whereSql
                ORDER BY a.created_at DESC
                LIMIT $perPage OFFSET $offset";
        $stmt = getDb()->prepare($sql);
        $stmt->execute($params);

        return [
            'rows' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ];
    }
}
