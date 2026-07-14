<?php
require_once __DIR__ . '/../../components/under-construction.php';
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin', 'admin']);

$user = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        header('Location: ' . BASE_URL . '/admin/alerts.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'mark_read') {
        $id = (int) $_POST['id'];
        Alert::markRead($id);
        AuditLog::record((int) $user['id'], 'alert_mark_read', "Marked alert #$id as read");
    }

    if ($action === 'mark_all_read') {
        Alert::markAllRead();
        AuditLog::record((int) $user['id'], 'alert_mark_all_read', 'Marked all alerts as read');
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        Alert::delete($id);
        AuditLog::record((int) $user['id'], 'alert_delete', "Deleted alert #$id");
    }

    header('Location: ' . BASE_URL . '/admin/alerts.php?' . http_build_query($_GET));
    exit;
}

$filters = [
    'type' => $_GET['type'] ?? '',
    'is_read' => $_GET['is_read'] ?? '',
];
$page = max(1, (int) ($_GET['page'] ?? 1));
$result = Alert::list($filters, $page, 20);
$totalPages = max(1, (int) ceil($result['total'] / $result['perPage']));

$typeOptions = ['low_moisture' => 'Low Moisture', 'low_battery' => 'Low Battery', 'pump_fail' => 'Pump Fail', 'schedule_conflict' => 'Schedule Conflict'];
$typeLabels = ['low_moisture' => 'bg-warning text-dark', 'low_battery' => 'bg-danger', 'pump_fail' => 'bg-dark', 'schedule_conflict' => 'bg-secondary'];

$pageTitle = 'Alerts';
$activePage = 'alerts';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Alerts & Notifications</h4>
    <form method="post" action="<?= BASE_URL ?>/admin/alerts.php">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="mark_all_read">
        <button type="submit" class="btn btn-sm btn-outline-secondary">Mark All Read</button>
    </form>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="<?= BASE_URL ?>/admin/alerts.php" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($typeOptions as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $filters['type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Status</label>
                <select name="is_read" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="0" <?= $filters['is_read'] === '0' ? 'selected' : '' ?>>Unread</option>
                    <option value="1" <?= $filters['is_read'] === '1' ? 'selected' : '' ?>>Read</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="<?= BASE_URL ?>/admin/alerts.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result['rows'])): ?>
                    <tr><td colspan="5" class="text-muted text-center py-4">No alerts found.</td></tr>
                <?php endif; ?>
                <?php foreach ($result['rows'] as $a): ?>
                    <tr class="<?= (int) $a['is_read'] === 0 ? 'fw-semibold' : '' ?>">
                        <td><span class="badge <?= $typeLabels[$a['type']] ?? 'bg-secondary' ?>"><?= htmlspecialchars($typeOptions[$a['type']] ?? $a['type']) ?></span></td>
                        <td><?= htmlspecialchars($a['message']) ?></td>
                        <td><?= (int) $a['is_read'] === 1 ? '<span class="text-muted">Read</span>' : '<span class="text-primary">Unread</span>' ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                        <td class="text-end">
                            <?php if ((int) $a['is_read'] === 0): ?>
                                <form method="post" action="<?= BASE_URL ?>/admin/alerts.php" class="d-inline">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark Read</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/alerts.php" class="d-inline" onsubmit="return confirm('Delete this alert?');">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<div class="text-muted small mt-2">
    Alert thresholds are currently configured in <code>config/device.php</code>
    (low moisture &lt; <?= ALERT_LOW_MOISTURE_PCT ?>%, low battery &lt; <?= ALERT_LOW_BATTERY_VOLTS ?>V).
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
