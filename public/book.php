<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

// Only logged-in supporters can book
if (!isset($_SESSION['user_id'])) {
    $fixture_id = isset($_GET['fixture_id']) ? (int) $_GET['fixture_id'] : 0;
    header("Location: login.php?redirect=" . urlencode("book.php?fixture_id=$fixture_id"));
    exit;
}

$fixture_id = isset($_GET['fixture_id']) ? (int) $_GET['fixture_id'] : 0;

if ($fixture_id <= 0) {
    header("Location: fixtures.php");
    exit;
}

// Load the fixture
$stmt = $pdo->prepare("SELECT * FROM fixtures WHERE fixture_id = :id");
$stmt->execute(['id' => $fixture_id]);
$fixture = $stmt->fetch();

if (!$fixture) {
    header("Location: fixtures.php");
    exit;
}

$tickets_left = $fixture['total_tickets'] - $fixture['tickets_sold'];
$is_sold_out  = $tickets_left <= 0;
$is_bookable  = $fixture['status'] === 'upcoming' && !$is_sold_out;

if (!$is_bookable) {
    header("Location: fixture_detail.php?id=$fixture_id");
    exit;
}

$errors = [];
$quantity = 1;

// Process the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

    if ($quantity < 1) {
        $errors[] = 'Please select at least 1 ticket.';
    } elseif ($quantity > 10) {
        $errors[] = 'You can only book up to 10 tickets per booking.';
    } elseif ($quantity > $tickets_left) {
        $errors[] = "Only $tickets_left tickets are still available.";
    }

    if (empty($errors)) {

        // Start a database transaction — either everything saves, or nothing does
        try {
            $pdo->beginTransaction();

            // 1. Re-check availability INSIDE the transaction (anti-oversell)
            $stmt = $pdo->prepare("SELECT total_tickets, tickets_sold
                                   FROM fixtures
                                   WHERE fixture_id = :id
                                   FOR UPDATE");
            $stmt->execute(['id' => $fixture_id]);
            $current = $stmt->fetch();

            $available_now = $current['total_tickets'] - $current['tickets_sold'];
            if ($quantity > $available_now) {
                throw new Exception("Sorry, only $available_now tickets are still available.");
            }

            // 2. Calculate the total
            $total_price = $quantity * $fixture['ticket_price'];

            // 3. Create the booking record (mock payment = always "completed")
            $stmt = $pdo->prepare("INSERT INTO bookings
                (user_id, fixture_id, quantity, total_price, payment_status)
                VALUES
                (:user_id, :fixture_id, :quantity, :total_price, 'completed')");

            $stmt->execute([
                'user_id'     => $_SESSION['user_id'],
                'fixture_id'  => $fixture_id,
                'quantity'    => $quantity,
                'total_price' => $total_price
            ]);

            $booking_id = $pdo->lastInsertId();

            // 4. Create one ticket per quantity, each with a unique QR token
            $stmt = $pdo->prepare("INSERT INTO tickets
                (booking_id, qr_token, status)
                VALUES
                (:booking_id, :qr_token, 'valid')");

            for ($i = 0; $i < $quantity; $i++) {
                // 32 random bytes => 64 hex characters — unguessable
                $qr_token = bin2hex(random_bytes(32));
                $stmt->execute([
                    'booking_id' => $booking_id,
                    'qr_token'   => $qr_token
                ]);
            }

            // 5. Update the fixture's tickets_sold count
            $stmt = $pdo->prepare("UPDATE fixtures
                                   SET tickets_sold = tickets_sold + :qty
                                   WHERE fixture_id = :id");
            $stmt->execute(['qty' => $quantity, 'id' => $fixture_id]);

                        // 6. If we got this far, commit the changes
            $pdo->commit();

            // 7. Send the confirmation "email" (saved as HTML file in dev mode)
            require_once __DIR__ . '/../includes/email_helper.php';

            // Reload the booking + tickets to pass to the mailer
            $stmt = $pdo->prepare("
                SELECT b.booking_id, b.quantity,
                       f.opposition, f.competition, f.match_date, f.kick_off_time, f.venue,
                       u.first_name, u.email
                FROM bookings b
                JOIN fixtures f ON b.fixture_id = f.fixture_id
                JOIN users u    ON b.user_id    = u.user_id
                WHERE b.booking_id = :booking_id
            ");
            $stmt->execute(['booking_id' => $booking_id]);
            $booking_data = $stmt->fetch();

            $stmt = $pdo->prepare("SELECT ticket_id, qr_token FROM tickets WHERE booking_id = :id ORDER BY ticket_id ASC");
            $stmt->execute(['id' => $booking_id]);
            $tickets_data = $stmt->fetchAll();

            sendBookingEmail($booking_data, $tickets_data);

            // Redirect to the success page
            header("Location: booking_success.php?booking_id=$booking_id");
            exit;


        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = $e->getMessage();
        }
    }
}

$page_title = 'Book tickets';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 600px;">

        <p style="margin-bottom: 15px;">
            <a href="fixture_detail.php?id=<?php echo (int) $fixture_id; ?>" style="color: #1a3a5c;">← Back to fixture</a>
        </p>

        <h2>Book your tickets</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div style="color: #4ca3dd; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                <?php echo htmlspecialchars($fixture['competition']); ?>
            </div>
            <h3 style="font-size: 20px; color: #1a3a5c; margin: 4px 0 12px;">
                <?php echo htmlspecialchars(CLUB_NAME); ?> vs <?php echo htmlspecialchars($fixture['opposition']); ?>
            </h3>
            <div style="color: #555; font-size: 15px;">
                <?php echo date('l, j F Y', strtotime($fixture['match_date'])); ?>
                · <?php echo date('H:i', strtotime($fixture['kick_off_time'])); ?>
                · £<?php echo number_format($fixture['ticket_price'], 2); ?> per ticket
            </div>
        </div>

        <form action="book.php?fixture_id=<?php echo (int) $fixture_id; ?>" method="POST" class="card" style="margin-top: 15px;">

            <div class="form-group">
                <label for="quantity">How many tickets?</label>
                <select id="quantity" name="quantity" required onchange="updateTotal()">
                    <?php
                    $max = min(10, $tickets_left);
                    for ($i = 1; $i <= $max; $i++):
                    ?>
                        <option value="<?php echo $i; ?>" <?php if ($i === $quantity) echo 'selected'; ?>>
                            <?php echo $i; ?> ticket<?php echo $i > 1 ? 's' : ''; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div style="background: #f4f6f8; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; font-size: 15px; color: #555;">
                    <span>Price per ticket</span>
                    <span>£<?php echo number_format($fixture['ticket_price'], 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: #1a3a5c; margin-top: 8px; border-top: 1px solid #ddd; padding-top: 8px;">
                    <span>Total</span>
                    <span id="total-display">£<?php echo number_format($fixture['ticket_price'] * $quantity, 2); ?></span>
                </div>
            </div>

            <p style="font-size: 13px; color: #777; margin-bottom: 15px;">
                ℹ️ This is a simulated payment for academic purposes. No real money will be charged.
            </p>

            <button type="submit" class="btn">Confirm booking &amp; pay</button>
        </form>

    </div>
</section>

<script>
    const price = <?php echo (float) $fixture['ticket_price']; ?>;
    function updateTotal() {
        const qty = parseInt(document.getElementById('quantity').value, 10);
        const total = (price * qty).toFixed(2);
        document.getElementById('total-display').textContent = '£' + total;
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
