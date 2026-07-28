<?php
// Alerts has been merged into the Logs & Alerts page; keep this redirect for old links/bookmarks.
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin', 'admin']);

header('Location: ' . BASE_URL . '/admin/logs.php?' . http_build_query(array_merge($_GET, ['tab' => 'alerts'])));
exit;
