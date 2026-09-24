<?php
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

        if ($action === 'test_run') {
            $id = (int) ($_POST['id'] ?? 0);
            $res = Schedule::triggerTestRun($id, (int) $user['id']);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($res);
                exit;
            }
            header('Location: ' . BASE_URL . '/admin/schedule.php?test_run=' . $id);
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
    <div class="col-12 col-lg-5">
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
                        <label class="form-label fw-semibold">Duration (seconds)</label>
                        <input type="number" name="duration_minutes" min="1" max="3600" class="form-control" required
                               value="<?= htmlspecialchars((string) ($editing['duration_minutes'] ?? '15')) ?>"
                               placeholder="e.g. 10, 15, 30">
                        <div class="form-text text-muted" style="font-size: 0.8rem;">
                            Presentation mode: duration is configured in seconds (e.g. 10s, 15s, 30s) for live demonstration.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Days of Week</label>
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            <?php
                            $selectedDays = $editing ? explode(',', $editing['days_of_week']) : [];
                            foreach ($dayOptions as $key => $label):
                            ?>
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="<?= $key ?>"
                                           id="day_<?= $key ?>" <?= in_array($key, $selectedDays, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="day_<?= $key ?>"><?= $label ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"><?= $editing ? 'Update' : 'Add Schedule' ?></button>
                    <?php if ($editing): ?>
                        <a href="<?= BASE_URL ?>/admin/schedule.php" class="btn btn-outline-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">All Schedules</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle text-nowrap">
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
                                <td><span class="badge bg-light text-dark border"><?= (int) $s['duration_minutes'] ?>s</span></td>
                                <td><?= htmlspecialchars(strtoupper(str_replace(',', ', ', $s['days_of_week']))) ?></td>
                                <td>
                                    <span class="badge <?= $s['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $s['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?php if ($s['is_active']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="startScheduleTestRun(<?= (int) $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['label'])) ?>', <?= (int) $s['duration_minutes'] ?>)"
                                                title="Trigger an immediate presentation test run of this schedule">
                                            ▶ Test Run
                                        </button>
                                    <?php endif; ?>
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

<!-- Schedule Presentation Test Run Loading Overlay -->
<div id="schedLoadingOverlay" style="display: none; position: fixed; inset: 0; z-index: 10500; background: rgba(15, 23, 42, 0.88); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card shadow-lg border-0 text-center" style="max-width: 460px; width: 100%; border-radius: 1rem; background: #1e293b; color: #f8fafc; border: 1px solid rgba(255, 255, 255, 0.1) !important;">
        <div class="card-body p-4 p-md-5">
            <div class="mb-3 position-relative d-inline-block">
                <div class="spinner-border text-success" style="width: 3.75rem; height: 3.75rem; border-width: 0.3em;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="position-absolute top-50 start-50 translate-middle fs-5" id="schedLoadingIcon">
                    💧
                </div>
            </div>
            
            <h5 class="fw-bold mb-1" id="schedLoadingTitle">Running Irrigation Schedule</h5>
            <p class="text-secondary small mb-3" id="schedLoadingSubtitle">
                Presentation Test Run active. Server commanding PUMP_ON to hardware for scheduled seconds cycle.
            </p>

            <div class="display-5 fw-bold text-success mb-2 font-monospace" id="schedLoadingTimer">
                --s
            </div>

            <div class="progress mb-3" style="height: 10px; background-color: rgba(255,255,255,0.1); border-radius: 5px; overflow: hidden;">
                <div id="schedLoadingBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%; transition: width 0.1s linear;"></div>
            </div>

            <div class="badge bg-dark border border-secondary text-info px-3 py-2 text-wrap" id="schedLoadingStep" style="font-size: 0.82rem; font-weight: 500;">
                Actuating pump relay & irrigating miniature rice field...
            </div>
        </div>
    </div>
</div>

<script>
function startScheduleTestRun(id, label, durationSec) {
    if (!confirm(`Trigger presentation test run for "${label}" (${durationSec} seconds)?`)) {
        return;
    }

    const overlay = document.getElementById('schedLoadingOverlay');
    const timerEl = document.getElementById('schedLoadingTimer');
    const barEl = document.getElementById('schedLoadingBar');
    const titleEl = document.getElementById('schedLoadingTitle');
    const subtitleEl = document.getElementById('schedLoadingSubtitle');
    const stepEl = document.getElementById('schedLoadingStep');

    if (titleEl) titleEl.textContent = `Schedule: ${label}`;
    if (subtitleEl) subtitleEl.textContent = `Presentation test run (${durationSec}s cycle). Server has dispatched PUMP_ON to hardware.`;
    if (timerEl) timerEl.textContent = durationSec.toFixed(1) + 's';
    if (barEl) barEl.style.width = '100%';
    if (overlay) overlay.style.display = 'flex';

    // Post to trigger
    const formData = new URLSearchParams();
    formData.append('action', 'test_run');
    formData.append('id', id);
    formData.append('csrf_token', '<?= Csrf::token() ?>');

    fetch('<?= BASE_URL ?>/admin/schedule.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    }).catch(err => console.error(err));

    // Countdown timer matching durationSec
    const totalDurationMs = durationSec * 1000;
    const intervalMs = 100;
    let elapsedMs = 0;

    const timer = setInterval(() => {
        elapsedMs += intervalMs;
        const remainingMs = Math.max(0, totalDurationMs - elapsedMs);
        const remainingSec = (remainingMs / 1000).toFixed(1);
        const percent = (remainingMs / totalDurationMs) * 100;

        if (timerEl) timerEl.textContent = remainingSec + 's';
        if (barEl) barEl.style.width = percent + '%';

        if (stepEl) {
            if (remainingMs > 5000) {
                stepEl.textContent = `Pumping standing water (Target duration: ${durationSec}s)...`;
            } else if (remainingMs > 1500) {
                stepEl.textContent = 'Approaching cycle completion & settling window...';
            } else {
                stepEl.textContent = 'Auto-stopping pump & resetting mode...';
            }
        }

        if (elapsedMs >= totalDurationMs) {
            clearInterval(timer);
            if (overlay) overlay.style.display = 'none';
            alert(`Schedule "${label}" completed its ${durationSec}-second presentation run!`);
            location.reload();
        }
    }, intervalMs);
}
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
