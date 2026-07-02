<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    header("Location: my_tickets.php");
    exit;
}

// Load booking details — and verify it belongs to the logged-in user
$stmt = $pdo->prepare("
    SELECT
        b.booking_id, b.quantity, b.total_price, b.booking_date,
        f.opposition, f.competition, f.match_date, f.kick_off_time, f.venue
    FROM bookings b
    JOIN fixtures f ON b.fixture_id = f.fixture_id
    WHERE b.booking_id = :booking_id
      AND b.user_id = :user_id
");
$stmt->execute([
    'booking_id' => $booking_id,
    'user_id'    => $_SESSION['user_id']
]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: my_tickets.php");
    exit;
}

// How many tickets were generated for this booking
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE booking_id = :id");
$stmt->execute(['id' => $booking_id]);
$ticket_count = $stmt->fetchColumn();

$page_title = 'Booking confirmed';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 600px;">

        <div class="card" style="border-top: 4px solid #28a745;">

            <div style="font-size: 40px; text-align: center; margin-bottom: 10px;">✓</div>

            <h2 style="text-align: center; color: #28a745; margin-bottom: 8px;">Booking confirmed!</h2>

            <p style="text-align: center; color: #555; margin-bottom: 25px;">
                Booking reference: <strong>#<?php echo (int) $booking['booking_id']; ?></strong>
            </p>

            <div style="background: #f4f6f8; padding: 18px; border-radius: 4px; margin-bottom: 20px;">
                <h3 style="color: #1a3a5c; font-size: 18px; margin-bottom: 8px;">
                    <?php echo htmlspecialchars(CLUB_NAME); ?> vs <?php echo htmlspecialchars($booking['opposition']); ?>
                </h3>
                <div style="color: #555; font-size: 14px; margin-bottom: 4px;">
                    <?php echo htmlspecialchars($booking['competition']); ?>
                </div>
                <div style="color: #555; font-size: 14px;">
                    📅 <?php echo date('l, j F Y', strtotime($booking['match_date'])); ?>
                    &nbsp; 🕒 <?php echo date('H:i', strtotime($booking['kick_off_time'])); ?>
                    <br>📍 <?php echo htmlspecialchars($booking['venue']); ?>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 16px; padding: 8px 0;">
                <span>Tickets purchased</span>
                <strong><?php echo (int) $booking['quantity']; ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 16px; padding: 8px 0; border-top: 1px solid #eee;">
                <span>Total paid</span>
                <strong>£<?php echo number_format($booking['total_price'], 2); ?></strong>
            </div>

            <div class="alert alert-success" style="margin-top: 20px;">
                ✓ <strong><?php echo (int) $ticket_count; ?></strong> digital ticket<?php echo $ticket_count > 1 ? 's have' : ' has'; ?> been generated.
                <br><small>QR codes and email delivery will be added in the next development step.</small>
            </div>

            <div style="text-align: center; margin-top: 25px;">
    <a href="view_ticket.php?booking_id=<?php echo (int) $booking['booking_id']; ?>" class="btn">View my tickets with QR</a>
    <a href="my_tickets.php" class="btn btn-secondary" style="margin-left: 8px;">All my bookings</a>
</div>


        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
