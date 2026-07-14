<?php
require_once __DIR__ . '/../../components/under-construction.php';
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireLogin();

$today = date('Y-m-d');
$defaultFrom = date('Y-m-d', strtotime('-6 days'));

$from = $_GET['from'] ?? $defaultFrom;
$to = $_GET['to'] ?? $today;

// Basic sanity check so a malformed date can't break the query.
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
    $from = $defaultFrom;
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
    $to = $today;
}
if ($from > $to) {
    [$from, $to] = [$to, $from];
}

$readings = SensorReading::rangeBetween($from, $to);
$events = IrrigationEvent::between($from, $to);

if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="irrigation-events_' . $from . '_to_' . $to . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id', 'schedule', 'trigger_type', 'started_at', 'ended_at', 'status', 'duration_minutes']);
    foreach ($events as $e) {
        $duration = '';
        if ($e['ended_at']) {
            $duration = round((strtotime($e['ended_at']) - strtotime($e['started_at'])) / 60, 1);
        }
        fputcsv($out, [
            $e['id'],
            $e['schedule_label'] ?? 'manual',
            $e['trigger_type'],
            $e['started_at'],
            $e['ended_at'] ?? '',
            $e['status'],
            $duration,
        ]);
    }
    fclose($out);
    exit;
}

$completedMinutes = 0;
foreach ($events as $e) {
    if ($e['ended_at']) {
        $completedMinutes += (strtotime($e['ended_at']) - strtotime($e['started_at'])) / 60;
    }
}

$chartLabels = array_map(fn($r) => $r['recorded_at'], $readings);
$chartMoisture = array_map(fn($r) => $r['soil_moisture'], $readings);
$chartBattery = array_map(fn($r) => $r['battery_voltage'], $readings);
$chartSolar = array_map(fn($r) => $r['solar_output'], $readings);

$pageTitle = 'Reports';
$activePage = 'reports';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Reports</h4>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="get" action="<?= BASE_URL ?>/admin/reports.php" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($from) ?>">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($to) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                <a href="<?= BASE_URL ?>/admin/reports.php?from=<?= htmlspecialchars($from) ?>&to=<?= htmlspecialchars($to) ?>&export=csv"
                   class="btn btn-sm btn-outline-secondary">Export CSV</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Irrigation Events</div>
                <div class="fs-3 fw-bold"><?= count($events) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Total Pump Runtime</div>
                <div class="fs-3 fw-bold"><?= round($completedMinutes) ?> min</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Sensor Readings</div>
                <div class="fs-3 fw-bold"><?= count($readings) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header">Sensor Trend</div>
    <div class="card-body">
        <?php if (empty($readings)): ?>
            <div class="text-muted text-center py-4">No sensor data for this range.</div>
        <?php else: ?>
            <div style="max-width: 100%; overflow-x: auto;">
                <canvas id="trendChart" height="90"></canvas>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">Irrigation Events</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Schedule</th>
                    <th>Trigger</th>
                    <th>Started</th>
                    <th>Ended</th>
                    <th>Duration</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr><td colspan="6" class="text-muted text-center py-4">No irrigation events for this range.</td></tr>
                <?php endif; ?>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['schedule_label'] ?? 'Manual') ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($e['trigger_type']) ?></span></td>
                        <td><?= htmlspecialchars($e['started_at']) ?></td>
                        <td><?= htmlspecialchars($e['ended_at'] ?? '—') ?></td>
                        <td><?= $e['ended_at'] ? round((strtotime($e['ended_at']) - strtotime($e['started_at'])) / 60, 1) . ' min' : '—' ?></td>
                        <td>
                            <span class="badge <?= $e['status'] === 'completed' ? 'bg-success' : ($e['status'] === 'running' ? 'bg-primary' : 'bg-warning text-dark') ?>">
                                <?= htmlspecialchars($e['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($readings)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('trendChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            {
                label: 'Soil Moisture (%)',
                data: <?= json_encode($chartMoisture) ?>,
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46,125,50,0.1)',
                tension: 0.3,
                spanGaps: true,
            },
            {
                label: 'Battery (V)',
                data: <?= json_encode($chartBattery) ?>,
                borderColor: '#1565c0',
                backgroundColor: 'rgba(21,101,192,0.1)',
                tension: 0.3,
                spanGaps: true,
            },
            {
                label: 'Solar Output (W)',
                data: <?= json_encode($chartSolar) ?>,
                borderColor: '#ef6c00',
                backgroundColor: 'rgba(239,108,0,0.1)',
                tension: 0.3,
                spanGaps: true,
            },
        ],
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: { x: { ticks: { maxTicksLimit: 10 } } },
    },
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
