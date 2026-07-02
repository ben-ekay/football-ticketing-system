<?php
require_once __DIR__ . '/../config/database.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$uid   = (int)$_SESSION['user_id'];
$error = '';

// Handle deletion BEFORE any HTML output (so the redirect works)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $password = $_POST['password'] ?? '';

    // Verify the password belongs to this account
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = :uid");
    $stmt->execute([':uid' => $uid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($password, $row['password_hash'])) {
        $error = 'Incorrect password. Your account has NOT been deleted.';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Delete tickets linked to this user's bookings
            $stmt = $pdo->prepare(
                "DELETE t FROM tickets t
                 JOIN bookings b ON t.booking_id = b.booking_id
                 WHERE b.user_id = :uid"
            );
            $stmt->execute([':uid' => $uid]);

            // 2. Delete the user's bookings
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE user_id = :uid");
            $stmt->execute([':uid' => $uid]);

            // 3. Delete the user record itself
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
            $stmt->execute([':uid' => $uid]);

            $pdo->commit();

            // Destroy the session and return to home
            $_SESSION = [];
            session_destroy();
            header('Location: index.php?deleted=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Something went wrong and your account was not deleted. Please try again.';
        }
    }
}

$page_title = 'Delete account';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width:560px; margin:3rem auto; padding:0 1.5rem;">
    <h1 style="font-family:'Bebas Neue',sans-serif; color:#0a2540; font-size:2.4rem; letter-spacing:1px; margin-bottom:.5rem;">
        Delete your account
    </h1>

    <div style="background:#fdecea; border-left:4px solid #dc3545; color:#721c24; padding:1rem 1.25rem; border-radius:6px; margin-bottom:1.5rem;">
        <strong>This cannot be undone.</strong> Deleting your account permanently removes your profile,
        your bookings and all of your digital tickets. You will not be able to recover them.
    </div>

    <?php if ($error): ?>
        <div style="background:#fdecea; border-left:4px solid #dc3545; color:#721c24; padding:.85rem 1.1rem; border-radius:6px; margin-bottom:1.25rem;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <p style="color:#333; line-height:1.6;">
        For your security, please enter your password to confirm. If you have any upcoming
        match tickets, make sure you no longer need them before continuing.
    </p>

    <form method="POST" style="margin-top:1.5rem;">
        <label for="password" style="display:block; font-weight:600; color:#0a2540; margin-bottom:.35rem;">
            Your password
        </label>
        <input type="password" id="password" name="password" required
               style="width:100%; padding:.7rem .9rem; border:1px solid #ccd6e0; border-radius:6px; font-size:1rem; box-sizing:border-box; margin-bottom:1.25rem;">

        <button type="submit" name="confirm_delete" value="1"
                onclick="return confirm('Are you absolutely sure? This will permanently delete your account and all your tickets.');"
                style="background:#dc3545; color:#fff; border:none; padding:.8rem 1.5rem; font-weight:700; border-radius:6px; cursor:pointer; letter-spacing:.5px;">
            PERMANENTLY DELETE MY ACCOUNT
        </button>

        <a href="my_tickets.php" style="display:inline-block; margin-left:1rem; color:#1a3a6e; text-decoration:none; font-weight:600;">
            Cancel
        </a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
