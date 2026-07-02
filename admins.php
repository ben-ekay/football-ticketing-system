<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_header.php';

// Auth check — only logged-in admins can manage admins
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$errors  = [];
$success = '';

// --- Handle DELETE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = (int)$_POST['delete_id'];

    if ($delId === (int)$_SESSION['admin_id']) {
        $errors[] = 'You cannot delete your own account while logged in.';
    } else {
        $count = (int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
        if ($count <= 1) {
            $errors[] = 'Cannot delete the last remaining administrator.';
        } else {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE admin_id = :id");
            $stmt->execute([':id' => $delId]);
            $success = 'Administrator deleted successfully.';
        }
    }
}

// --- Handle CREATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
        $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (strlen($full_name) < 2) {
        $errors[] = 'Full name is required.';
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers and underscores.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = :u");
        $stmt->execute([':u' => $username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'That username is already taken.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO admins (username, full_name, password_hash) VALUES (:u, :f, :p)");
        $stmt->execute([':u' => $username, ':f' => $full_name, ':p' => $hash]);
        $success = "Administrator '" . htmlspecialchars($username) . "' created successfully.";
    }
}

// --- Fetch all admins ---
$admins = $pdo->query("SELECT admin_id, username, full_name FROM admins ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
  .admins-wrap { max-width: 900px; margin: 2rem auto; padding: 0 1.5rem; }
  .admins-wrap h1 { font-family: 'Bebas Neue', sans-serif; color: #0a2540; font-size: 2.5rem; letter-spacing: 1px; margin: 0 0 .25rem; }
  .admins-wrap .subtitle { color: #555; margin-bottom: 2rem; }
  .card { background: #fff; border-radius: 12px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 4px 16px rgba(10,37,64,.08); border-left: 4px solid #00d4ff; }
  .card h2 { font-family: 'Bebas Neue', sans-serif; color: #1a3a6e; font-size: 1.5rem; margin: 0 0 1rem; letter-spacing: .5px; }
  .form-group { margin-bottom: 1rem; }
  .form-group label { display: block; font-weight: 600; color: #0a2540; margin-bottom: .35rem; }
  .form-group input { width: 100%; padding: .65rem .85rem; border: 1px solid #ccd6e0; border-radius: 6px; font-size: 1rem; font-family: inherit; box-sizing: border-box; }
  .form-group input:focus { outline: none; border-color: #00d4ff; box-shadow: 0 0 0 3px rgba(0,212,255,.15); }
  .form-group small { display: block; color: #777; font-size: .85rem; margin-top: .25rem; }
  .btn-primary { background: #00d4ff; color: #0a2540; border: none; padding: .75rem 1.5rem; font-weight: 700; border-radius: 6px; cursor: pointer; letter-spacing: .5px; font-size: .95rem; }
  .btn-primary:hover { background: #00b8e0; }
  .btn-danger { background: #dc3545; color: #fff; border: none; padding: .4rem .9rem; border-radius: 4px; cursor: pointer; font-size: .85rem; font-weight: 600; }
  .btn-danger:hover { background: #c82333; }
  .alert { padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem; }
  .alert-error   { background: #fdecea; border-left: 4px solid #dc3545; color: #721c24; }
  .alert-success { background: #e6f7ed; border-left: 4px solid #2d8a3e; color: #155724; }
  .alert ul { margin: .5rem 0 0; padding-left: 1.25rem; }
  .admin-table { width: 100%; border-collapse: collapse; }
  .admin-table th, .admin-table td { padding: .75rem; text-align: left; border-bottom: 1px solid #eaf2f8; }
  .admin-table th { background: #0a2540; color: #fff; font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; }
  .badge { background: #2d8a3e; color: #fff; padding: .2rem .6rem; border-radius: 12px; font-size: .75rem; font-weight: 600; }
  .badge-current { background: #00d4ff; color: #0a2540; }
</style>

<div class="admins-wrap">
    <h1>Manage Administrators</h1>
    <p class="subtitle">Create new admin accounts and manage existing ones. Passwords are stored as bcrypt hashes.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Could not complete request:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Create new administrator</h2>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="3" pattern="[a-zA-Z0-9_]+" autocomplete="off">
                <small>Letters, numbers and underscores only. Minimum 3 characters.</small>
            </div>
            <div class="form-group">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" required minlength="2" autocomplete="off">
                <small>Real name of the administrator (e.g. "Jane Smith"). Shown in the admin panel.</small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                <small>Minimum 8 characters.</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
            </div>
            <button type="submit" name="create" value="1" class="btn-primary">CREATE ADMINISTRATOR</button>
        </form>
    </div>

    <div class="card">
        <h2>Existing administrators (<?= count($admins) ?>)</h2>
        <table class="admin-table">
            <thead>
    <tr><th>Username</th><th>Full name</th><th>Status</th><th>Actions</th></tr>
</thead>

            <tbody>
                <?php foreach ($admins as $a): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($a['username']) ?></strong></td>
<td><?= htmlspecialchars($a['full_name'] ?? '—') ?></td>
<td>
                            <?php if ((int)$a['admin_id'] === (int)$_SESSION['admin_id']): ?>
                                <span class="badge badge-current">You</span>
                            <?php else: ?>
                                <span class="badge">Active</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int)$a['admin_id'] !== (int)$_SESSION['admin_id']): ?>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete administrator <?= htmlspecialchars($a['username'], ENT_QUOTES) ?>? This cannot be undone.');">
                                    <input type="hidden" name="delete_id" value="<?= (int)$a['admin_id'] ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <em style="color:#999">—</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
