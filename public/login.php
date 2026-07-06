<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../src/services/AuthService.php';

if (Auth::check()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $user = AuthService::attempt($email, $password);

        if ($user) {
            Auth::login($user);
            AuditLog::record((int) $user['id'], 'login', 'User logged in');
            header('Location: /admin/dashboard.php');
            exit;
        }

        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - WBACFSPWI Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-light">
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm" style="width: 360px;">
        <div class="card-body p-4">
            <h5 class="card-title mb-3 text-center">WBACFSPWI Admin</h5>
            <p class="text-muted text-center small mb-4">Solar-Powered Water Irrigation Controller</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="/login.php">
                <?= Csrf::field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Log In</button>
            </form>
            <div class="text-center mt-3">
                <a href="/forgot-password.php" class="small">Forgot password?</a>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
