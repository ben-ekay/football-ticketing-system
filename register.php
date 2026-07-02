<?php
$page_title = 'Register';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

// Variables to hold form data and any error messages
$errors = [];
$first_name = $last_name = $email = $phone = '';

// Only process the form if it has been submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Grab values from the form and trim whitespace
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $password   = $_POST['password']   ?? '';
    $confirm    = $_POST['confirm']    ?? '';

    // 2. Validate
    if ($first_name === '') {
        $errors[] = 'First name is required.';
    }
    if ($last_name === '') {
        $errors[] = 'Last name is required.';
    }
    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // 3. Check the email isn't already taken (only if other checks passed)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already registered. Try logging in instead.';
        }
    }

    // 4. If everything is valid, create the account
    if (empty($errors)) {

        // Hash the password before storing (NEVER store plain text passwords)
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert using a prepared statement (protects against SQL injection)
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password_hash, phone)
            VALUES (:first_name, :last_name, :email, :password_hash, :phone)
        ");

        $stmt->execute([
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'email'         => $email,
            'password_hash' => $password_hash,
            'phone'         => $phone
        ]);

        // Redirect to the login page with a success message
        header("Location: login.php?registered=1");
        exit;
    }
}
?>

<section class="section">
    <div class="container" style="max-width: 500px;">

        <h2>Create your account</h2>
        <p style="margin-bottom: 25px;">Register to book match tickets and receive your digital QR ticket by email.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Please fix the following:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="card">

            <div class="form-group">
                <label for="first_name">First name *</label>
                <input type="text" id="first_name" name="first_name"
                       value="<?php echo htmlspecialchars($first_name); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last name *</label>
                <input type="text" id="last_name" name="last_name"
                       value="<?php echo htmlspecialchars($last_name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email"
                       value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone (optional)</label>
                <input type="tel" id="phone" name="phone"
                       value="<?php echo htmlspecialchars($phone); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password * (minimum 8 characters)</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm">Confirm password *</label>
                <input type="password" id="confirm" name="confirm" required>
            </div>

            <button type="submit" class="btn">Create account</button>

            <p style="margin-top: 18px; font-size: 14px;">
                Already have an account? <a href="login.php" style="color: #1a3a5c;">Log in</a>
            </p>
        </form>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
