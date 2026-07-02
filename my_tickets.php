<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Load all the user's bookings with fixture info, newest first
$stmt = $pdo->prepare("
    SELECT
        b.booking_id, b.quantity, b.total_price, b.booking_date,
        f.fixture_id, f.opposition, f.competition,
        f.match_date, f.kick_off_time, f.venue, f.status
    FROM bookings b
    JOIN fixtures f ON b.fixture_id = f.fixture_id
    WHERE b.user_id = :user_id
    ORDER BY f.match_date DESC, f.kick_off_time DESC
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

$page_title = 'My Tickets';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container">

        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2>
        <p style="margin-bottom: 25px; color: #555;">All your bookings, past and upcoming.</p>

        <?php if (empty($bookings)): ?>

            <div class="card">
                <p>You haven't booked any tickets yet.</p>
                <p style="margin-top: 12px;">
                    <a href="fixtures.php" class="btn">Browse upcoming fixtures</a>
                </p>
            </div>

        <?php else: ?>

            <?php foreach ($bookings as $booking):
                $is_past = strtotime($booking['match_date']) < strtotime('today');
            ?>

                <div class="card" style="<?php if ($is_past) echo 'opacity: 0.7;'; ?>">

                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">

                        <div style="flex: 1; min-width: 250px;">
                            <div style="color: #4ca3dd; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <?php echo htmlspecialchars($booking['competition']); ?>
                                <?php if ($is_past): ?>
                                    <span style="color: #999; margin-left: 6px;">· Past fixture</span>
                                <?php endif; ?>
                            </div>

                            <h3 style="font-size: 19px; color: #1a3a5c; margin: 4px 0 8px;">
                                <?php echo htmlspecialchars(CLUB_NAME); ?> vs <?php echo htmlspecialchars($booking['opposition']); ?>
                            </h3>

                            <div style="color: #555; font-size: 14px;">
                                📅 <?php echo date('l, j F Y', strtotime($booking['match_date'])); ?>
                                · 🕒 <?php echo date('H:i', strtotime($booking['kick_off_time'])); ?>
                            </div>
                            <div style="color: #555; font-size: 14px; margin-top: 2px;">
                                📍 <?php echo htmlspecialchars($booking['venue']); ?>
                            </div>

                            <div style="margin-top: 10px; font-size: 14px; color: #555;">
                                Booking #<?php echo (int) $booking['booking_id']; ?>
                                · <?php echo (int) $booking['quantity']; ?> ticket<?php echo $booking['quantity'] > 1 ? 's' : ''; ?>
                                · £<?php echo number_format($booking['total_price'], 2); ?>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <?php if (!$is_past): ?>
                                <a href="booking_success.php?booking_id=<?php echo (int) $booking['booking_id']; ?>" class="btn btn-secondary" style="font-size: 14px; padding: 8px 16px;">
                                    View details
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</section>
<div style="text-align:center; margin:2.5rem 0 1rem;">
    <a href="delete_account.php" style="color:#999; font-size:.9rem; text-decoration:underline;">
        Delete my account
    </a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
