<?php
// ============================================
// GoalTicket - Database Connection
// ============================================

// Load local database credentials
$localConfig = __DIR__ . '/database.local.php';

if (!file_exists($localConfig)) {
    die('Database configuration file not found.');
}

require_once $localConfig;

// Connect using PDO
try {
    $dsn = "mysql:host=" . DB_HOST .
           ";port=" . DB_PORT .
           ";dbname=" . DB_NAME .
           ";charset=utf8mb4";

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);

} catch (PDOException $e) {
    die("Database connection failed.");
}
?>