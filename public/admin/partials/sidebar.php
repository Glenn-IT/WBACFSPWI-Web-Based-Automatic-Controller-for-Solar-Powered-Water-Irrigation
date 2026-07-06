<?php
// Expects $activePage to be set by the including page (e.g. 'dashboard').
$activePage = $activePage ?? '';
$user = Auth::user();

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'href' => '/admin/dashboard.php', 'roles' => ['super_admin', 'admin', 'viewer']],
    'schedule'  => ['label' => 'Schedule', 'href' => '/admin/schedule.php', 'roles' => ['super_admin', 'admin']],
    'reports'   => ['label' => 'Reports', 'href' => '/admin/reports.php', 'roles' => ['super_admin', 'admin', 'viewer']],
    'logs'      => ['label' => 'Logs', 'href' => '/admin/logs.php', 'roles' => ['super_admin', 'admin']],
    'alerts'    => ['label' => 'Alerts', 'href' => '/admin/alerts.php', 'roles' => ['super_admin', 'admin']],
    'override'  => ['label' => 'Manual Override', 'href' => '/admin/override.php', 'roles' => ['super_admin', 'admin']],
    'users'     => ['label' => 'User Management', 'href' => '/admin/users.php', 'roles' => ['super_admin']],
    'profile'   => ['label' => 'Profile', 'href' => '/admin/profile.php', 'roles' => ['super_admin', 'admin', 'viewer']],
];
?>
<nav class="sidebar p-3" id="sidebar" style="width: 240px;">
    <div class="brand mb-4 px-2">WBACFSPWI</div>
    <ul class="nav nav-pills flex-column mb-auto">
        <?php foreach ($navItems as $key => $item): ?>
            <?php if (!in_array($user['role'], $item['roles'], true)) continue; ?>
            <li class="nav-item">
                <a href="<?= $item['href'] ?>" class="nav-link <?= $activePage === $key ? 'active' : '' ?>">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <hr class="text-secondary">
    <div class="px-2 small text-secondary mb-2">
        Signed in as<br>
        <strong class="text-light"><?= htmlspecialchars($user['name']) ?></strong>
        <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($user['role']) ?></span>
    </div>
    <a href="/logout.php" class="btn btn-sm btn-outline-light w-100">Log Out</a>
</nav>
<main class="flex-grow-1 p-4">
