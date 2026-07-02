<?php
require_once __DIR__ . '/../config/config.php';

// Empty the session data
$_SESSION = [];

// Destroy the session cookie on the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session on the server
session_destroy();

// Redirect to login with a friendly message
header("Location: login.php?loggedout=1");
exit;
?>
