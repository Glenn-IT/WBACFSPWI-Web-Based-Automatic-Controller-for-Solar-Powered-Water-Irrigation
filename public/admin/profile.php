<?php
require_once __DIR__ . '/../../components/under-construction.php';
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireLogin();

$user = Auth::user();
$fresh = User::findById((int) $user['id']);
$error = null;
$success = null;
$securityQuestions = require __DIR__ . '/../../config/security_questions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_info') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please provide a valid name and email.';
            } else {
                $existing = User::findByEmail($email);
                if ($existing && (int) $existing['id'] !== (int) $user['id']) {
                    $error = 'That email is already in use.';
                } else {
                    User::updateProfile((int) $user['id'], $name, $email);
                    AuditLog::record((int) $user['id'], 'profile_update', 'Updated name/email');
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $success = 'Profile updated successfully.';
                    $fresh = User::findById((int) $user['id']);
                }
            }
        }

        if ($action === 'update_password') {
            $current = (string) ($_POST['current_password'] ?? '');
            $new = (string) ($_POST['new_password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (!password_verify($current, $fresh['password_hash'])) {
                $error = 'Current password is incorrect.';
            } elseif (strlen($new) < 8) {
                $error = 'New password must be at least 8 characters.';
            } elseif ($new !== $confirm) {
                $error = 'New password and confirmation do not match.';
            } else {
                User::updatePassword((int) $user['id'], password_hash($new, PASSWORD_DEFAULT));
                AuditLog::record((int) $user['id'], 'password_change', 'Changed own password');
                $success = 'Password updated successfully.';
            }
        }

        if ($action === 'update_security') {
            $current = (string) ($_POST['current_password'] ?? '');
            $question = $_POST['security_question'] ?? '';
            $answer = trim($_POST['security_answer'] ?? '');
            $confirmAnswer = trim($_POST['security_answer_confirm'] ?? '');

            if (!password_verify($current, $fresh['password_hash'])) {
                $error = 'Current password is incorrect.';
            } elseif (!array_key_exists($question, $securityQuestions)) {
                $error = 'Please choose a security question.';
            } elseif ($answer === '' || mb_strlen($answer) < 2) {
                $error = 'Please provide an answer.';
            } elseif (mb_strtolower($answer) !== mb_strtolower($confirmAnswer)) {
                $error = 'Answers do not match.';
            } else {
                User::updateSecurityQuestion((int) $user['id'], $question, $answer);
                AuditLog::record((int) $user['id'], 'security_question_update', 'Updated security question');
                $success = 'Security question updated.';
                $fresh = User::findById((int) $user['id']);
            }
        }
    }
}

$pageTitle = 'Profile';
$activePage = 'profile';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">Profile</h4>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Account Info</div>
            <div class="card-body">
                <form method="post" action="<?= BASE_URL ?>/admin/profile.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="update_info">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= htmlspecialchars($fresh['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required
                               value="<?= htmlspecialchars($fresh['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($fresh['role']) ?>" disabled>
                    </div>
                    <div class="mb-3 text-muted small">
                        Last login: <?= htmlspecialchars($fresh['last_login_at'] ?? 'Never') ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <form method="post" action="<?= BASE_URL ?>/admin/profile.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="update_password">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">Security Question (used for Forgot Password)</div>
            <div class="card-body">
                <div class="text-muted small mb-3">
                    Currently set:
                    <strong><?= htmlspecialchars($securityQuestions[$fresh['security_question']] ?? 'Not set') ?></strong>
                </div>
                <form method="post" action="<?= BASE_URL ?>/admin/profile.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="update_security">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Security Question</label>
                        <select name="security_question" class="form-select" required>
                            <option value="">Choose a question...</option>
                            <?php foreach ($securityQuestions as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $fresh['security_question'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer</label>
                        <input type="text" name="security_answer" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Answer</label>
                        <input type="text" name="security_answer_confirm" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Security Question</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
