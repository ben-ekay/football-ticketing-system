<?php
require_once __DIR__ . '/../config/config.php';

// Wipe session data
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Location: " . SITE_URL . "/admin/login.php?loggedout=1");
exit;
?>