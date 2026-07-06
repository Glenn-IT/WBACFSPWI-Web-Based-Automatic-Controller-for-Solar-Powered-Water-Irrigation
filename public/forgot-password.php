<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (Auth::check()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$securityQuestions = require __DIR__ . '/../config/security_questions.php';

$error = null;
$success = null;

$step = 'email';
if (!empty($_SESSION['pwreset_verified'])) {
    $step = 'reset';
} elseif (!empty($_SESSION['pwreset_user_id'])) {
    $step = 'answer';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'lookup') {
            $email = trim($_POST['email'] ?? '');
            $user = User::findByEmail($email);

            if (!$user || empty($user['security_question'])) {
                $error = 'No account matches that email, or no security question has been set up for it. Contact an administrator.';
                $step = 'email';
            } else {
                $_SESSION['pwreset_user_id'] = (int) $user['id'];
                unset($_SESSION['pwreset_verified']);
                $step = 'answer';
            }
        }

        if ($action === 'answer') {
            $userId = $_SESSION['pwreset_user_id'] ?? null;
            $selectedQuestion = $_POST['security_question'] ?? '';
            $answer = (string) ($_POST['answer'] ?? '');
            $accountUser = $userId ? User::findById((int) $userId) : null;

            if (!$userId || !$accountUser || empty($accountUser['security_question'])) {
                $error = 'Session expired, please start again.';
                $step = 'email';
            } elseif (
                $selectedQuestion !== $accountUser['security_question']
                || !User::verifySecurityAnswer((int) $userId, $answer)
            ) {
                // Deliberately generic — doesn't reveal whether the question or the answer was wrong.
                $error = 'Incorrect question or answer.';
                $step = 'answer';
            } else {
                $_SESSION['pwreset_verified'] = true;
                $step = 'reset';
            }
        }

        if ($action === 'reset') {
            $userId = $_SESSION['pwreset_user_id'] ?? null;
            $verified = $_SESSION['pwreset_verified'] ?? false;
            $new = (string) ($_POST['new_password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (!$userId || !$verified) {
                $error = 'Session expired, please start again.';
                $step = 'email';
            } elseif (strlen($new) < 8) {
                $error = 'New password must be at least 8 characters.';
                $step = 'reset';
            } elseif ($new !== $confirm) {
                $error = 'Passwords do not match.';
                $step = 'reset';
            } else {
                User::updatePassword((int) $userId, password_hash($new, PASSWORD_DEFAULT));
                AuditLog::record((int) $userId, 'password_reset', 'Password reset via forgot-password flow');
                unset($_SESSION['pwreset_user_id'], $_SESSION['pwreset_verified']);
                $success = 'Password reset successfully. You can now log in.';
                $step = 'done';
            }
        }
    }
}

if (($step === 'answer' || $step === 'reset') && !empty($_SESSION['pwreset_user_id'])) {
    $u = User::findById((int) $_SESSION['pwreset_user_id']);
    if (!$u || empty($u['security_question'])) {
        // Security question got cleared mid-flow (e.g. by an admin) — bail out safely.
        unset($_SESSION['pwreset_user_id'], $_SESSION['pwreset_verified']);
        $step = 'email';
        $error = 'Session expired, please start again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - WBACFSPWI Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-light">
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm" style="width: 400px;">
        <div class="card-body p-4">
            <h5 class="card-title mb-3 text-center">Forgot Password</h5>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($step === 'email'): ?>
                <p class="text-muted small">Enter your account email to begin recovery.</p>
                <form method="post" action="/forgot-password.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="lookup">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Continue</button>
                </form>
            <?php elseif ($step === 'answer'): ?>
                <p class="text-muted small">Select your security question and provide the answer to continue.</p>
                <form method="post" action="/forgot-password.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="answer">
                    <div class="mb-3">
                        <label class="form-label">Security Question</label>
                        <select name="security_question" class="form-select" required>
                            <option value="">Choose a question...</option>
                            <?php foreach ($securityQuestions as $key => $label): ?>
                                <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <input type="text" name="answer" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify</button>
                </form>
            <?php elseif ($step === 'reset'): ?>
                <p class="text-muted small">Answer verified. Set a new password.</p>
                <form method="post" action="/forgot-password.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="reset">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <a href="/login.php" class="btn btn-primary w-100">Back to Log In</a>
                </div>
            <?php endif; ?>

            <?php if ($step !== 'done'): ?>
                <div class="text-center mt-3">
                    <a href="/login.php" class="small">Back to Log In</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
