<?php

// ============================================
// GoalTicket - General Configuration
// ============================================

// Public site information
define('SITE_NAME', 'GoalTicket');
define('CLUB_NAME', 'Newcastle Benfield FC');

// Load local configuration
$localConfig = __DIR__ . '/config.local.php';

if (!file_exists($localConfig)) {
    die('Local application configuration file not found.');
}

require_once $localConfig;

// Default timezone
date_default_timezone_set('Europe/London');

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>