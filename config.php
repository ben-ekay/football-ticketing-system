<?php

// ============================================
// GoalTicket - General Configuration
// ============================================

// Site information
define('SITE_NAME', 'GoalTicket');
define('SITE_URL', 'http://localhost:8888/goalticket');
define('CLUB_NAME', 'Newcastle Benfield FC'); // Change to your real client club later

// Secret key used to sign QR tokens (change this in production!)
define('QR_SECRET', 'change-this-to-a-long-random-string-later');

// Default timezone (UK)
date_default_timezone_set('Europe/London');

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
