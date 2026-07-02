<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

// If the user is already logged in, send them to their tickets page
if (isset($_SESSION['user_id'])) {
    header("Location: my_tickets.php");
    exit;
}

$page_title = 'Log In';
$errors = [];
$email = '';

// Process the form only when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Grab and clean the inputs
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    // 2. Basic validation
    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both your email and password.';
    }

    // 3. If validation passed, look up the user
    if (empty($errors)) {

        $stmt = $pdo->prepare("SELECT user_id, first_name, password_hash
                               FROM users
                               WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // 4. Check the password against the stored hash
        //    NOTE: we use the same generic error message whether the email
        //    or the password was wrong. This prevents attackers from
        //    learning which emails are registered (NFR3, NFR5).
        if ($user && password_verify($password, $user['password_hash'])) {

            // Login successful — start the session
            session_regenerate_id(true); // protects against session fixation
            $_SESSION['user_id']    = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];

                        // Send them to the requested redirect URL, or My Tickets by default
            $redirect = $_GET['redirect'] ?? 'my_tickets.php';
            // Safety: only allow redirects to local pages
            if (!preg_match('/^[a-zA-Z0-9_]+\.php(\?.*)?$/', $redirect)) {
                $redirect = 'my_tickets.php';
            }
            header("Location: " . $redirect);
            exit;


        } else {
            $errors[] = 'Incorrect email or password.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 480px;">

        <h2>Log in</h2>

        <?php if (isset($_GET['registered']) && $_GET['registered'] == '1'): ?>
            <div class="alert alert-success">
                ✓ Your account has been created. You can now log in.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['loggedout']) && $_GET['loggedout'] == '1'): ?>
            <div class="alert alert-success">
                You have been logged out.
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
                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Log in</button>

            <p style="margin-top: 18px; font-size: 14px;">
                Don't have an account yet?
                <a href="register.php" style="color: #1a3a5c;">Register here</a>
            </p>
        </form>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
