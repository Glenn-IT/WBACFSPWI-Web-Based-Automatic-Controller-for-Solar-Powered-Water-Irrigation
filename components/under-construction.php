<?php

define('CURRENT_VERSION', 'v1.06');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Under Construction - WBACFSPWI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm text-center" style="width: 420px;">
        <div class="card-body p-5">
            <div style="font-size: 4rem;" aria-hidden="true">&#128119;</div>
            <div class="mb-3">
                <span class="badge bg-warning text-dark"><?= CURRENT_VERSION ?></span>
            </div>
            <h4 class="card-title mb-2">Under Construction</h4>
            <p class="text-muted mb-4">
                This page is not yet available in the current version of the system.
                It will be unlocked in an upcoming release.
            </p>
            <button type="button" class="btn btn-primary" onclick="history.back()">Go Back</button>
        </div>
    </div>
</div>
</body>
</html>
<?php
exit;
