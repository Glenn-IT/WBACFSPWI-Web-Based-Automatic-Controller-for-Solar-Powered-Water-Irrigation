<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../src/services/AuthService.php';

if (Auth::check()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

$error = null;

const LOGIN_MAX_ATTEMPTS = 3;
const LOGIN_LOCKOUT_SECONDS = 30;

// Expire a finished lockout so the attempt counter starts fresh.
if (isset($_SESSION['login_locked_until']) && time() >= $_SESSION['login_locked_until']) {
    unset($_SESSION['login_locked_until']);
    $_SESSION['login_attempts'] = 0;
}

$lockRemaining = isset($_SESSION['login_locked_until'])
    ? max(0, $_SESSION['login_locked_until'] - time())
    : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($lockRemaining > 0) {
        $error = 'Too many failed attempts. Please wait before trying again.';
    } elseif (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $user = AuthService::attempt($email, $password);

        if ($user) {
            Auth::login($user);
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
            AuditLog::record((int) $user['id'], 'login', 'User logged in');
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }

        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

        if ($_SESSION['login_attempts'] >= LOGIN_MAX_ATTEMPTS) {
            $_SESSION['login_locked_until'] = time() + LOGIN_LOCKOUT_SECONDS;
            $lockRemaining = LOGIN_LOCKOUT_SECONDS;
            AuditLog::record(null, 'login_lockout', "Login locked for $email after " . LOGIN_MAX_ATTEMPTS . ' failed attempts');
            $error = 'Too many failed attempts. Login is locked for ' . LOGIN_LOCKOUT_SECONDS . ' seconds.';
        } else {
            $remaining = LOGIN_MAX_ATTEMPTS - $_SESSION['login_attempts'];
            $error = 'Invalid email or password. ' . $remaining . ' attempt' . ($remaining === 1 ? '' : 's') . ' remaining.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Web Based Automatic Controller for Solar Powered Water Irrigation</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>
<body class="bg-light">
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm" style="width: 360px;">
        <div class="card-body p-4">
            <div class="text-center mb-3">
                <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo" class="login-logo mb-2">
                <p class="text-muted small mb-0">Web Based Automatic Controller for Solar Powered Water Irrigation</p>
            </div>
            <p class="text-muted text-center small mb-4">Admin Login</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/login.php">
                <?= Csrf::field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="login-email" class="form-control" required autofocus
                           <?= $lockRemaining > 0 ? 'disabled' : '' ?>>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="login-password" class="form-control" required
                           <?= $lockRemaining > 0 ? 'disabled' : '' ?>>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="login-btn"
                        <?= $lockRemaining > 0 ? 'disabled' : '' ?>>Log In</button>
            </form>
            <div class="text-center mt-3">
                <a href="<?= BASE_URL ?>/forgot-password.php" class="small">Forgot password?</a>
            </div>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
<?php if ($lockRemaining > 0): ?>
<script>
(function () {
    const btn = document.getElementById('login-btn');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');
    let remaining = <?= (int) $lockRemaining ?>;

    function tick() {
        if (remaining <= 0) {
            btn.disabled = false;
            emailInput.disabled = false;
            passwordInput.disabled = false;
            btn.textContent = 'Log In';
            return;
        }
        btn.textContent = 'Locked (' + remaining + 's)';
        remaining--;
        setTimeout(tick, 1000);
    }

    tick();
})();
</script>
<?php endif; ?>
</body>
</html>
