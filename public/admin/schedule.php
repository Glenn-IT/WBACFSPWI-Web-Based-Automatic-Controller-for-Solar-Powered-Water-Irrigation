<?php
require_once __DIR__ . '/../../components/under-construction.php';
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin', 'admin']);

$user = Auth::user();
$error = null;
$editing = null;

$dayOptions = ['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'delete') {
            $id = (int) $_POST['id'];
            $existing = Schedule::find($id);
            Schedule::delete($id);
            AuditLog::record((int) $user['id'], 'schedule_delete', "Deleted schedule #$id (" . ($existing['label'] ?? '') . ')');
            header('Location: ' . BASE_URL . '/admin/schedule.php');
            exit;
        }

        if ($action === 'toggle') {
            $id = (int) $_POST['id'];
            $newState = (int) $_POST['is_active'] === 1 ? false : true;
            Schedule::setActive($id, $newState);
            AuditLog::record((int) $user['id'], 'schedule_toggle', "Schedule #$id set to " . ($newState ? 'active' : 'inactive'));
            header('Location: ' . BASE_URL . '/admin/schedule.php');
            exit;
        }

        if ($action === 'save') {
            $label = trim($_POST['label'] ?? '');
            $startTime = $_POST['start_time'] ?? '';
            $duration = (int) ($_POST['duration_minutes'] ?? 0);
            $days = array_intersect($_POST['days'] ?? [], array_keys($dayOptions));
            $id = (int) ($_POST['id'] ?? 0);

            if ($label === '' || $startTime === '' || $duration <= 0 || empty($days)) {
                $error = 'Please fill in all fields and select at least one day.';
            } else {
                $data = [
                    'label' => $label,
                    'start_time' => $startTime,
                    'duration_minutes' => $duration,
                    'days_of_week' => implode(',', $days),
                    'is_active' => 1,
                    'created_by' => $user['id'],
                ];

                if ($id > 0) {
                    Schedule::update($id, $data);
                    AuditLog::record((int) $user['id'], 'schedule_update', "Updated schedule #$id ($label)");
                } else {
                    $newId = Schedule::create($data);
                    AuditLog::record((int) $user['id'], 'schedule_create', "Created schedule #$newId ($label)");
                }

                header('Location: ' . BASE_URL . '/admin/schedule.php');
                exit;
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editing = Schedule::find((int) $_GET['edit']);
}

$schedules = Schedule::all();

$pageTitle = 'Schedule';
$activePage = 'schedule';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Irrigation Schedule</h4>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header"><?= $editing ? 'Edit Schedule' : 'Add Schedule' ?></div>
            <div class="card-body">
                <form method="post" action="<?= BASE_URL ?>/admin/schedule.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="save">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control" required
                               value="<?= htmlspecialchars($editing['label'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control" required
                               value="<?= htmlspecialchars($editing['start_time'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" min="1" class="form-control" required
                               value="<?= htmlspecialchars((string) ($editing['duration_minutes'] ?? '')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Days of Week</label>
                        <?php
                        $selectedDays = $editing ? explode(',', $editing['days_of_week']) : [];
                        foreach ($dayOptions as $key => $label):
                        ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="days[]" value="<?= $key ?>"
                                       id="day_<?= $key ?>" <?= in_array($key, $selectedDays, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="day_<?= $key ?>"><?= $label ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="btn btn-primary"><?= $editing ? 'Update' : 'Add Schedule' ?></button>
                    <?php if ($editing): ?>
                        <a href="<?= BASE_URL ?>/admin/schedule.php" class="btn btn-outline-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header">All Schedules</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Start</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedules)): ?>
                            <tr><td colspan="6" class="text-muted text-center py-4">No schedules yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['label']) ?></td>
                                <td><?= htmlspecialchars(substr($s['start_time'], 0, 5)) ?></td>
                                <td><?= (int) $s['duration_minutes'] ?> min</td>
                                <td><?= htmlspecialchars(strtoupper(str_replace(',', ', ', $s['days_of_week']))) ?></td>
                                <td>
                                    <span class="badge <?= $s['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $s['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/admin/schedule.php?edit=<?= (int) $s['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="post" action="<?= BASE_URL ?>/admin/schedule.php" class="d-inline">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                        <input type="hidden" name="is_active" value="<?= (int) $s['is_active'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <?= $s['is_active'] ? 'Disable' : 'Enable' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="<?= BASE_URL ?>/admin/schedule.php" class="d-inline"
                                          onsubmit="return confirm('Delete this schedule?');">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
