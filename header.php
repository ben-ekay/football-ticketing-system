<?php
// Make sure config is loaded
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
        <!-- PWA — Progressive Web App support -->
    <link rel="manifest" href="<?php echo SITE_URL; ?>/manifest.json">
    <meta name="theme-color" content="#0a2540">
    <meta name="description" content="Buy matchday tickets and view QR codes for Newcastle Benfield FC.">

    <!-- iOS-specific PWA meta tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GoalTicket">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/icons/icon-180.png">

    <!-- Android / Chrome -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="GoalTicket">
    <!-- Service Worker registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo SITE_URL; ?>/sw.js', {
                    scope: '<?php echo SITE_URL; ?>/'
                })
                .then(reg => console.log('[SW] Registered with scope:', reg.scope))
                .catch(err => console.error('[SW] Registration failed:', err));
            });
        }
    </script>

</head>
<body>

<header class="site-header">
    <div class="container">
        <h1>
            <a href="<?php echo SITE_URL; ?>/public/index.php">
                Goal<span>Ticket</span>
            </a>
        </h1>

       <nav class="main-nav">
    <ul>
        <li><a href="<?php echo SITE_URL; ?>/public/index.php">Home</a></li>
        <li><a href="<?php echo SITE_URL; ?>/public/fixtures.php">Fixtures</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo SITE_URL; ?>/public/my_tickets.php">My Tickets</a></li>
        <?php endif; ?>

        <li><a href="<?php echo SITE_URL; ?>/public/support.php">Help</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo SITE_URL; ?>/public/logout.php">Log Out</a></li>
        <?php else: ?>
            <li><a href="<?php echo SITE_URL; ?>/public/login.php">Log In</a></li>
            <li><a href="<?php echo SITE_URL; ?>/public/register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

    </div>
</header>

<main>
