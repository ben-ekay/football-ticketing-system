<?php
// Admin guard: include this at the top of every admin page
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . SITE_URL . "/admin/login.php");
    exit;
}
?>
