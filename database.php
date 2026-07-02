<?php
// ============================================
// GoalTicket - Database Connection
// ============================================
// This file creates a single connection to the MySQL database
// that can be reused across the entire application.

// Database credentials (MAMP default values)
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'goalticket_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// Connect using PDO (PHP Data Objects) — safer than mysqli
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);

} catch (PDOException $e) {
    // If connection fails, stop everything and show a clear error
    die("Database connection failed: " . $e->getMessage());
}
?>
