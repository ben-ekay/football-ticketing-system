<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

// If already logged in as admin, send to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both your username and password.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT admin_id, username, full_name, role, password_hash
                               FROM admins
                               WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']        = $admin['admin_id'];
            $_SESSION['admin_username']  = $admin['username'];
            $_SESSION['admin_full_name'] = $admin['full_name'];
            $_SESSION['admin_role']      = $admin['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = 'Incorrect username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<header class="admin-header">
    <div class="container">
        <h1><a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Goal<span>Ticket</span> <small>Admin</small></a></h1>
    </div>
</header>

<main class="admin-main">
    <div class="container" style="max-width: 480px;">

        <h2>Admin log in</h2>
        <?php if (isset($_GET['loggedout']) && $_GET['loggedout'] === '1'): ?>
    <div class="alert alert-success">
        You have been logged out successfully.
    </div>
<?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="card">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Log in to admin</button>

            <p style="margin-top: 18px; font-size: 14px;">
                <a href="<?php echo SITE_URL; ?>/public/index.php" style="color: #1a3a5c;">← Back to main site</a>
            </p>
        </form>

    </div>
</main>

</body>
</html>
