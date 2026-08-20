<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/qr_helper.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=my_tickets.php");
    exit;
}

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
if ($booking_id <= 0) {
    header("Location: my_tickets.php");
    exit;
}

// Load booking + ensure it belongs to the logged-in user
$stmt = $pdo->prepare("
    SELECT b.booking_id, b.quantity,
           f.opposition, f.competition, f.match_date, f.kick_off_time, f.venue, f.ticket_price,
           u.first_name, u.last_name, u.email
    FROM bookings b
    JOIN fixtures f ON b.fixture_id = f.fixture_id
    JOIN users u    ON b.user_id    = u.user_id
    WHERE b.booking_id = :bid AND b.user_id = :uid
");
$stmt->execute(['bid' => $booking_id, 'uid' => $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: my_tickets.php");
    exit;
}

// Load tickets
$stmt = $pdo->prepare("SELECT ticket_id, qr_token, status FROM tickets WHERE booking_id = :bid ORDER BY ticket_id ASC");
$stmt->execute(['bid' => $booking_id]);
$tickets = $stmt->fetchAll();

// Helper: generate simulated seating data for each ticket
// Non-league clubs don't typically have seat numbers but it gives a professional look
function simulateSeat($ticket_id) {
    // Deterministic: same ticket_id always produces same seat
    srand($ticket_id);
    $entrances = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J'];
    $entrance = $entrances[rand(0, count($entrances) - 1)];
    $area = rand(100, 300);
    $row = rand(1, 30);
    $seat = rand(100, 999);
    srand(); // reset
    return ['entrance' => $entrance, 'area' => $area, 'row' => $row, 'seat' => $seat];
}

$customer_ref = 'GT-' . str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT);

$page_title = 'Your tickets';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* ============================================
       Wallet-style ticket
       ============================================ */
    .wallet-wrap {
        max-width: 420px;
        margin: 40px auto;
        padding: 0 16px;
    }

    .wallet-wrap .print-controls {
        text-align: center;
        margin-bottom: 24px;
    }

    .wallet-ticket {
        background: var(--stadium-dark);
        color: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(10, 37, 64, 0.35);
        margin-bottom: 28px;
        position: relative;
    }

    /* Top bar with club name + date/time */
    .wt-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 22px;
        background: linear-gradient(135deg, var(--stadium-dark) 0%, var(--stadium-blue) 100%);
        position: relative;
        overflow: hidden;
    }
    .wt-top::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background-image: repeating-linear-gradient(
            135deg, transparent, transparent 30px,
            rgba(255, 255, 255, 0.04) 30px, rgba(255, 255, 255, 0.04) 60px
        );
        pointer-events: none;
    }
    .wt-club {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 2;
    }
    .wt-club-badge {
        width: 36px;
        height: 36px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 20px;
        color: var(--stadium-dark);
        font-weight: 900;
    }
    .wt-club-name {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 15px;
    }
    .wt-date {
        text-align: right;
        position: relative;
        z-index: 2;
    }
    .wt-date-day {
        font-size: 11px;
        opacity: 0.85;
        letter-spacing: 1px;
        text-transform: uppercase;
    }
    .wt-date-time {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 22px;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* Hero stripe with opposition */
    .wt-hero {
        background: linear-gradient(135deg, var(--pitch-green-dark) 0%, var(--stadium-blue) 50%, var(--electric-cyan) 100%);
        padding: 30px 22px;
        position: relative;
        overflow: hidden;
    }
    .wt-hero::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background:
            radial-gradient(circle at 80% 30%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
            radial-gradient(circle at 20% 70%, rgba(0, 0, 0, 0.15) 0%, transparent 50%);
        pointer-events: none;
    }
    .wt-vs-label {
        font-size: 11px;
        opacity: 0.7;
        letter-spacing: 2px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }
    .wt-opposition {
        font-family: 'Bebas Neue', sans-serif;
        font-size: 42px;
        line-height: 1;
        margin-top: 4px;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
    }
    .wt-competition {
        display: inline-block;
        margin-top: 12px;
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        position: relative;
        z-index: 2;
        backdrop-filter: blur(4px);
    }

    /* Details rows */
    .wt-details {
        padding: 20px 22px;
    }
    .wt-row {
        display: grid;
        grid-template-columns: 1fr 1.6fr 1fr;
        gap: 14px;
        margin-bottom: 18px;
    }
    .wt-row.cols-4 {
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }
    .wt-row.cols-2 {
        grid-template-columns: 1fr 1.6fr;
    }
    .wt-cell .wt-label {
        font-size: 10px;
        opacity: 0.65;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 3px;
    }
    .wt-cell .wt-value {
        font-weight: 700;
        font-size: 14px;
    }

    /* Dashed separator */
    .wt-divider {
        position: relative;
        height: 30px;
        background: var(--stadium-dark);
    }
    .wt-divider::before,
    .wt-divider::after {
        content: '';
        position: absolute;
        width: 30px;
        height: 30px;
        background: var(--crowd-cream);
        border-radius: 50%;
        top: 0;
    }
    .wt-divider::before { left: -15px; }
    .wt-divider::after { right: -15px; }
    .wt-divider .wt-dashes {
        position: absolute;
        top: 50%;
        left: 25px;
        right: 25px;
        border-top: 2px dashed rgba(255, 255, 255, 0.25);
        transform: translateY(-50%);
    }

    /* QR section */
    .wt-qr-section {
        padding: 24px 22px 28px;
        text-align: center;
        background: var(--stadium-dark);
    }
    .wt-qr-box {
        background: #fff;
        padding: 16px;
        border-radius: 12px;
        display: inline-block;
        margin-bottom: 12px;
    }
    .wt-qr-box img {
        display: block;
        width: 180px;
        height: 180px;
    }
    .wt-qr-caption {
        font-size: 12px;
        opacity: 0.85;
        letter-spacing: 0.5px;
    }
    .wt-qr-id {
        font-size: 10px;
        opacity: 0.55;
        margin-top: 4px;
        font-family: monospace;
    }

    /* "Used" overlay */
    .wt-used {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(10, 37, 64, 0.7);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        backdrop-filter: blur(2px);
    }
    .wt-used .stamp {
        background: rgba(217, 83, 79, 0.95);
        color: #fff;
        padding: 14px 32px;
        font-family: 'Bebas Neue', sans-serif;
        font-size: 32px;
        letter-spacing: 4px;
        border: 4px solid #fff;
        transform: rotate(-15deg);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    /* Print */
    @media print {
        body * { visibility: hidden; }
        .wallet-wrap, .wallet-wrap * { visibility: visible; }
        .wallet-wrap { position: absolute; left: 0; top: 0; width: 100%; }
        .print-controls { display: none; }
        .wallet-ticket { box-shadow: none; page-break-after: always; }
    }
</style>

<div class="wallet-wrap">

    <div class="print-controls">
        <button onclick="window.print()" class="btn">🖨️ Print / Save as PDF</button>
        <a href="my_tickets.php" class="btn btn-secondary" style="margin-left: 8px;">← My tickets</a>
    </div>

    <?php foreach ($tickets as $index => $ticket):
        $seat = simulateSeat($ticket['ticket_id']);
        $is_used = $ticket['status'] === 'used';
    ?>
        <div class="wallet-ticket">

            <?php if ($is_used): ?>
                <div class="wt-used">
                    <div class="stamp">USED</div>
                </div>
            <?php endif; ?>

            <!-- Top bar -->
            <div class="wt-top">
                <div class="wt-club">
                    <div class="wt-club-badge">G</div>
                    <div class="wt-club-name"><?php echo htmlspecialchars(CLUB_NAME); ?></div>
                </div>
                <div class="wt-date">
                    <div class="wt-date-day"><?php echo strtoupper(date('d M Y', strtotime($booking['match_date']))); ?></div>
                    <div class="wt-date-time"><?php echo date('h:i A', strtotime($booking['kick_off_time'])); ?></div>
                </div>
            </div>

            <!-- Hero stripe -->
            <div class="wt-hero">
                <div class="wt-vs-label">VS</div>
                <div class="wt-opposition"><?php echo htmlspecialchars($booking['opposition']); ?></div>
                <div class="wt-competition"><?php echo htmlspecialchars($booking['competition']); ?></div>
            </div>

            <!-- Details: venue + attendee + ref -->
            <div class="wt-details">
                <div class="wt-row">
                    <div class="wt-cell">
                        <div class="wt-label">Venue</div>
                        <div class="wt-value"><?php echo htmlspecialchars($booking['venue']); ?></div>
                    </div>
                    <div class="wt-cell">
                        <div class="wt-label">Attendee</div>
                        <div class="wt-value"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></div>
                    </div>
                    <div class="wt-cell">
                        <div class="wt-label">Customer ref</div>
                        <div class="wt-value"><?php echo $customer_ref; ?></div>
                    </div>
                </div>

                <!-- Entrance / Area / Row / Seat -->
                <div class="wt-row cols-4">
                    <div class="wt-cell">
                        <div class="wt-label">Entrance</div>
                        <div class="wt-value"><?php echo $seat['entrance']; ?></div>
                    </div>
                    <div class="wt-cell">
                        <div class="wt-label">Area</div>
                        <div class="wt-value"><?php echo $seat['area']; ?></div>
                    </div>
                    <div class="wt-cell">
                        <div class="wt-label">Row</div>
                        <div class="wt-value"><?php echo $seat['row']; ?></div>
                    </div>
                    <div class="wt-cell">
                        <div class="wt-label">Seat</div>
                        <div class="wt-value"><?php echo $seat['seat']; ?></div>
                    </div>
                </div>
            </div>

            <!-- Divider with notches -->
            <div class="wt-divider">
                <div class="wt-dashes"></div>
            </div>

            <!-- QR section -->
            <div class="wt-qr-section">
                <div class="wt-qr-box">
                    <img src="<?php echo generateQrDataUri($ticket['qr_token']); ?>" alt="QR ticket">
                </div>
                <div class="wt-qr-caption">
                    Ticket <?php echo $index + 1; ?> of <?php echo count($tickets); ?> · Show at the turnstile
                </div>
                <div class="wt-qr-id">#<?php echo str_pad($ticket['ticket_id'], 6, '0', STR_PAD_LEFT); ?></div>
            </div>

        </div>
    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
