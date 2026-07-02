<?php require_once __DIR__ . '/admin_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | Admin' : 'Admin'; ?> | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
        <!-- PWA — Progressive Web App support -->
    <link rel="manifest" href="<?php echo SITE_URL; ?>/manifest.json">
    <meta name="theme-color" content="#0a2540">
    <meta name="description" content="GoalTicket admin panel — manage fixtures and scan tickets.">

    <!-- iOS-specific PWA meta tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GoalTicket Admin">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>/assets/icons/icon-180.png">

    <!-- Android / Chrome -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="GoalTicket Admin">
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
<body class="admin-body">

<header class="admin-header">
    <div class="container">
        <h1><a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Goal<span>Ticket</span> <small>Admin</small></a></h1>

        <nav class="admin-nav">
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Dashboard</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/fixtures.php">Fixtures</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/scan.php">Scan Tickets</a></li>
<li><a href="<?php echo SITE_URL; ?>/admin/admins.php">Admins</a></li>
<li><a href="<?php echo SITE_URL; ?>/admin/support.php">Help</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/logout.php">Log Out</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">
