<?php
require_once __DIR__ . '/../config/database.php';
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';

// Quick stats from the database
$total_users    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_fixtures = $pdo->query("SELECT COUNT(*) FROM fixtures WHERE status = 'upcoming'")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$tickets_sold   = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
?>

<h2 style="color: #1a3a5c; margin-bottom: 20px;">
    Welcome, <?php echo htmlspecialchars($_SESSION['admin_full_name']); ?>
</h2>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Registered Supporters</div>
        <div class="stat-value"><?php echo (int) $total_users; ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Upcoming Fixtures</div>
        <div class="stat-value"><?php echo (int) $total_fixtures; ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Bookings</div>
        <div class="stat-value"><?php echo (int) $total_bookings; ?></div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Tickets Sold</div>
        <div class="stat-value"><?php echo (int) $tickets_sold; ?></div>
    </div>
</div>

<div class="card">
    <h3 style="color: #1a3a5c; margin-bottom: 12px;">Quick actions</h3>
    <p style="margin-bottom: 15px;">From here you can manage everything related to matchday ticketing.</p>
    <a href="fixtures.php" class="btn" style="margin-right: 8px;">Manage Fixtures</a>
    <a href="scan.php" class="btn">Scan Tickets at the Gate</a>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
