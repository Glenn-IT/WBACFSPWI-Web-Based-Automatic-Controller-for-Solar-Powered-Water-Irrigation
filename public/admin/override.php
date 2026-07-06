<?php
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin', 'admin']);

$user = Auth::user();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $action = $_POST['pump_action'] ?? '';
        $reason = trim($_POST['reason'] ?? '');
        $autoRevert = (int) ($_POST['auto_revert_minutes'] ?? 60);

        if (!in_array($action, ['on', 'off'], true)) {
            $error = 'Invalid action.';
        } elseif ($autoRevert < 1 || $autoRevert > 1440) {
            $error = 'Auto-revert must be between 1 and 1440 minutes.';
        } else {
            Override::create((int) $user['id'], $action, $reason ?: null, $autoRevert);
            AuditLog::record((int) $user['id'], 'manual_override', "Pump forced $action for {$autoRevert}min" . ($reason ? " ($reason)" : ''));
            header('Location: /admin/override.php');
            exit;
        }
    }
}

$current = Override::latest();
$history = Override::recent(15);

$pageTitle = 'Manual Override';
$activePage = 'override';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Manual Override</h4>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header">Force Pump State</div>
            <div class="card-body">
                <?php if ($current && $current['still_active']): ?>
                    <div class="alert alert-info">
                        Active override: pump forced <strong><?= strtoupper($current['action']) ?></strong>
                        by <?= htmlspecialchars($current['user_name'] ?? 'unknown') ?>,
                        expires <?= (int) $current['auto_revert_minutes'] ?> min after
                        <?= htmlspecialchars($current['created_at']) ?>.
                        The device applies this until it auto-reverts to schedule.
                    </div>
                <?php else: ?>
                    <div class="text-muted small mb-3">No active override — device is following its normal schedule.</div>
                <?php endif; ?>

                <form method="post" action="/admin/override.php">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="pump_action" class="form-select" required>
                            <option value="on">Force Pump ON</option>
                            <option value="off">Force Pump OFF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Auto-revert after (minutes)</label>
                        <input type="number" name="auto_revert_minutes" class="form-control" min="1" max="1440" value="60" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <input type="text" name="reason" class="form-control" maxlength="255">
                    </div>
                    <button type="submit" class="btn btn-danger">Apply Override</button>
                </form>
                <div class="text-muted small mt-3">
                    The device picks this up on its next schedule poll and reverts to the normal schedule
                    automatically once the auto-revert window elapses.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header">Override History</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Reason</th>
                            <th>By</th>
                            <th>Auto-revert</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr><td colspan="5" class="text-muted text-center py-4">No overrides yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($history as $o): ?>
                            <tr>
                                <td><span class="badge <?= $o['action'] === 'on' ? 'bg-success' : 'bg-secondary' ?>"><?= strtoupper($o['action']) ?></span></td>
                                <td><?= htmlspecialchars($o['reason'] ?? '') ?></td>
                                <td><?= htmlspecialchars($o['user_name'] ?? 'unknown') ?></td>
                                <td><?= (int) $o['auto_revert_minutes'] ?> min</td>
                                <td><?= htmlspecialchars($o['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
