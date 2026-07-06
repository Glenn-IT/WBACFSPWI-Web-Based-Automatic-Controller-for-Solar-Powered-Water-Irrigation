<?php
// Expects $pageTitle and optionally $activePage to be set by the including page.
$pageTitle = $pageTitle ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - WBACFSPWI Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<button type="button" class="btn btn-dark d-md-none mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation">
    &#9776;
</button>
<div class="sidebar-backdrop d-md-none" id="sidebarBackdrop"></div>
<div class="d-flex">
