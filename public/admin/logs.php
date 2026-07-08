<?php
require_once __DIR__ . '/../../components/under-construction.php';
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin', 'admin']);

$filters = [
    'action' => trim($_GET['action'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
];
$filters = array_filter($filters, fn($v) => $v !== '');

$page = max(1, (int) ($_GET['page'] ?? 1));
$result = AuditLog::list($filters, $page, 25);
$totalPages = max(1, (int) ceil($result['total'] / $result['perPage']));

$pageTitle = 'Logs';
$activePage = 'logs';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Audit Logs</h4>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="<?= BASE_URL ?>/admin/logs.php" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Action contains</label>
                <input type="text" name="action" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($filters['action'] ?? '') ?>" placeholder="e.g. schedule_update">
            </div>
            <div class="col-md-3">
                <label class="form-label small">From date</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small">To date</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="<?= BASE_URL ?>/admin/logs.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result['rows'])): ?>
                    <tr><td colspan="5" class="text-muted text-center py-4">No log entries found.</td></tr>
                <?php endif; ?>
                <?php foreach ($result['rows'] as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['user_name'] ?? 'System') ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['action']) ?></span></td>
                        <td><?= htmlspecialchars($row['details']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($row['ip_address'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalPages > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small"><?= $result['total'] ?> total entries</span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
