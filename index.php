<?php
$page_title = 'Home';
require_once __DIR__ . '/../includes/header.php';
?>
<?php if (isset($_GET['deleted'])): ?>
    <div style="background:#e6f7ed; border-left:4px solid #2d8a3e; color:#155724; padding:1rem; text-align:center; margin-bottom:1rem;">
        Your account has been permanently deleted. We're sorry to see you go.
    </div>
<?php endif; ?>

<section class="hero">
    <div class="container">
        <h2>Match tickets, made simple.</h2>
        <p>Book your seat for upcoming home fixtures at <?php echo CLUB_NAME; ?>, get a digital ticket with a QR code, and walk straight through the turnstile on matchday.</p>
        <a href="<?php echo SITE_URL; ?>/public/fixtures.php" class="btn btn-secondary">View Upcoming Fixtures</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>How it works</h2>

        <div class="card">
            <h3>1. Browse fixtures</h3>
            <p>See all our upcoming home matches with dates, kick-off times and ticket prices.</p>
        </div>

        <div class="card">
            <h3>2. Book your tickets</h3>
            <p>Choose how many tickets you need and complete a quick, secure checkout.</p>
        </div>

        <div class="card">
            <h3>3. Scan and enter</h3>
            <p>Receive a unique QR code by email. Show it at the turnstile and you're in — no queues, no paper tickets.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
