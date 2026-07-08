<?php
require_once __DIR__ . '/../../config/bootstrap.php';
Auth::requireRole(['super_admin']);

$user = Auth::user();
$error = null;
$success = null;

$roleOptions = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'viewer' => 'Viewer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verify($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid request, please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = (string) ($_POST['password'] ?? '');
            $role = $_POST['role'] ?? 'viewer';

            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please provide a valid name and email.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif (!array_key_exists($role, $roleOptions)) {
                $error = 'Invalid role.';
            } elseif (User::findByEmail($email)) {
                $error = 'That email is already in use.';
            } else {
                $newId = User::create($name, $email, password_hash($password, PASSWORD_DEFAULT), $role);
                AuditLog::record((int) $user['id'], 'user_create', "Created user #$newId ($email, $role)");
                $success = 'User created.';
            }
        }

        if ($action === 'update_role') {
            $id = (int) $_POST['id'];
            $role = $_POST['role'] ?? 'viewer';
            if (!array_key_exists($role, $roleOptions)) {
                $error = 'Invalid role.';
            } elseif ($id === (int) $user['id']) {
                $error = 'You cannot change your own role.';
            } else {
                User::updateRole($id, $role);
                AuditLog::record((int) $user['id'], 'user_role_change', "Set user #$id role to $role");
                $success = 'Role updated.';
            }
        }

        if ($action === 'toggle_active') {
            $id = (int) $_POST['id'];
            $newState = (int) $_POST['is_active'] === 1 ? false : true;
            if ($id === (int) $user['id']) {
                $error = 'You cannot deactivate your own account.';
            } else {
                User::setActive($id, $newState);
                AuditLog::record((int) $user['id'], 'user_toggle_active', "User #$id set to " . ($newState ? 'active' : 'inactive'));
                $success = 'User status updated.';
            }
        }

        if ($action === 'delete') {
            $id = (int) $_POST['id'];
            if ($id === (int) $user['id']) {
                $error = 'You cannot delete your own account.';
            } else {
                $existing = User::findById($id);
                User::delete($id);
                AuditLog::record((int) $user['id'], 'user_delete', "Deleted user #$id (" . ($existing['email'] ?? '') . ')');
                $success = 'User deleted.';
            }
        }
    }
}

$users = User::all();

$pageTitle = 'User Management';
$activePage = 'users';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/sidebar.php';
?>

<h4 class="mb-4">User Management</h4>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">Add User</div>
            <div class="card-body">
                <form method="post" action="<?= BASE_URL ?>/admin/users.php">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <?php foreach ($roleOptions as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">All Users</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php if ((int) $u['id'] === (int) $user['id']): ?>
                                        <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($u['role']) ?></span>
                                    <?php else: ?>
                                        <form method="post" action="<?= BASE_URL ?>/admin/users.php" class="d-inline-flex gap-1">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="action" value="update_role">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <?php foreach ($roleOptions as $key => $label): ?>
                                                    <option value="<?= $key ?>" <?= $u['role'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $u['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($u['last_login_at'] ?? 'Never') ?></td>
                                <td class="text-end">
                                    <?php if ((int) $u['id'] !== (int) $user['id']): ?>
                                        <form method="post" action="<?= BASE_URL ?>/admin/users.php" class="d-inline">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="action" value="toggle_active">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <input type="hidden" name="is_active" value="<?= (int) $u['is_active'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                        <form method="post" action="<?= BASE_URL ?>/admin/users.php" class="d-inline"
                                              onsubmit="return confirm('Delete this user?');">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">(you)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
