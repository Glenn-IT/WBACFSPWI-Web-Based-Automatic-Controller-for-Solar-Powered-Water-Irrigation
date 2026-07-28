<?php
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin', 'admin']);

$user = Auth::user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        header('Location: ' . BASE_URL . '/admin/logs.php?tab=alerts');
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

    $redirectQuery = $_GET;
    $redirectQuery['tab'] = 'alerts';
    header('Location: ' . BASE_URL . '/admin/logs.php?' . http_build_query($redirectQuery));
    exit;
}

$tab = ($_GET['tab'] ?? 'alerts') === 'logs' ? 'logs' : 'alerts';

// Alerts tab
$alertFilters = [
    'type' => $_GET['type'] ?? '',
    'is_read' => $_GET['is_read'] ?? '',
];
$alertPage = max(1, (int) ($_GET['apage'] ?? 1));
$alertResult = Alert::list($alertFilters, $alertPage, 20);
$alertTotalPages = max(1, (int) ceil($alertResult['total'] / $alertResult['perPage']));

$typeOptions = ['low_moisture' => 'Low Moisture', 'low_battery' => 'Low Battery', 'pump_fail' => 'Pump Fail', 'schedule_conflict' => 'Schedule Conflict'];
$typeLabels = ['low_moisture' => 'bg-warning text-dark', 'low_battery' => 'bg-danger', 'pump_fail' => 'bg-dark', 'schedule_conflict' => 'bg-secondary'];

// Audit Logs tab
$logFilters = [
    'action' => trim($_GET['action_q'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
];
$logFilters = array_filter($logFilters, fn($v) => $v !== '');
$logPage = max(1, (int) ($_GET['lpage'] ?? 1));
$logResult = AuditLog::list($logFilters, $logPage, 25);
$logTotalPages = max(1, (int) ceil($logResult['total'] / $logResult['perPage']));

$pageTitle = 'Logs & Alerts';
$activePage = 'logs';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Logs & Alerts</h4>
    <?php if ($tab === 'alerts'): ?>
        <form method="post" action="<?= BASE_URL ?>/admin/logs.php">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="mark_all_read">
            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark All Read</button>
        </form>
    <?php endif; ?>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'alerts' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/logs.php?tab=alerts">
            Alerts
            <?php if ((int) Alert::countUnread() > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= Alert::countUnread() ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'logs' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/logs.php?tab=logs">Audit Logs</a>
    </li>
</ul>

<?php if ($tab === 'alerts'): ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="<?= BASE_URL ?>/admin/logs.php" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="alerts">
            <div class="col-auto">
                <label class="form-label small mb-1">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($typeOptions as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $alertFilters['type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">Status</label>
                <select name="is_read" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="0" <?= $alertFilters['is_read'] === '0' ? 'selected' : '' ?>>Unread</option>
                    <option value="1" <?= $alertFilters['is_read'] === '1' ? 'selected' : '' ?>>Read</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="<?= BASE_URL ?>/admin/logs.php?tab=alerts" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                <?php if (empty($alertResult['rows'])): ?>
                    <tr><td colspan="5" class="text-muted text-center py-4">No alerts found.</td></tr>
                <?php endif; ?>
                <?php foreach ($alertResult['rows'] as $a): ?>
                    <tr class="<?= (int) $a['is_read'] === 0 ? 'fw-semibold' : '' ?>">
                        <td><span class="badge <?= $typeLabels[$a['type']] ?? 'bg-secondary' ?>"><?= htmlspecialchars($typeOptions[$a['type']] ?? $a['type']) ?></span></td>
                        <td><?= htmlspecialchars($a['message']) ?></td>
                        <td><?= (int) $a['is_read'] === 1 ? '<span class="text-muted">Read</span>' : '<span class="text-primary">Unread</span>' ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                        <td class="text-end">
                            <?php if ((int) $a['is_read'] === 0): ?>
                                <form method="post" action="<?= BASE_URL ?>/admin/logs.php?<?= htmlspecialchars(http_build_query(['tab' => 'alerts'])) ?>" class="d-inline">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark Read</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" action="<?= BASE_URL ?>/admin/logs.php?<?= htmlspecialchars(http_build_query(['tab' => 'alerts'])) ?>" class="d-inline" onsubmit="return confirm('Delete this alert?');">
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

<?php if ($alertTotalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm">
            <?php for ($i = 1; $i <= $alertTotalPages; $i++): ?>
                <li class="page-item <?= $i === $alertPage ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['tab' => 'alerts', 'apage' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<div class="text-muted small mt-2">
    Alert thresholds are currently configured in <code>config/device.php</code>
    (low moisture &lt; <?= ALERT_LOW_MOISTURE_PCT ?>%, low battery &lt; <?= ALERT_LOW_BATTERY_VOLTS ?>V).
</div>

<?php else: ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="<?= BASE_URL ?>/admin/logs.php" class="row g-2 align-items-end">
            <input type="hidden" name="tab" value="logs">
            <div class="col-md-3">
                <label class="form-label small">Action contains</label>
                <input type="text" name="action_q" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($logFilters['action'] ?? '') ?>" placeholder="e.g. schedule_update">
            </div>
            <div class="col-md-3">
                <label class="form-label small">From date</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($logFilters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small">To date</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="<?= htmlspecialchars($logFilters['date_to'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="<?= BASE_URL ?>/admin/logs.php?tab=logs" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                <?php if (empty($logResult['rows'])): ?>
                    <tr><td colspan="5" class="text-muted text-center py-4">No log entries found.</td></tr>
                <?php endif; ?>
                <?php foreach ($logResult['rows'] as $row): ?>
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
    <?php if ($logTotalPages > 1): ?>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small"><?= $logResult['total'] ?> total entries</span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php for ($i = 1; $i <= $logTotalPages; $i++): ?>
                        <li class="page-item <?= $i === $logPage ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['tab' => 'logs', 'lpage' => $i]))) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
