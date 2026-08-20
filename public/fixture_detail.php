<?php
require_once __DIR__ . '/../config/database.php';

$fixture_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($fixture_id <= 0) {
    header("Location: fixtures.php");
    exit;
}

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

$page_title = $fixture['opposition'] . ' (' . date('j M', strtotime($fixture['match_date'])) . ')';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width: 800px;">

        <p style="margin-bottom: 15px;">
            <a href="fixtures.php" style="color: #1a3a5c;">← Back to all fixtures</a>
        </p>

        <div class="card">

            <div style="color: #4ca3dd; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                <?php echo htmlspecialchars($fixture['competition']); ?>
            </div>

            <h2 style="font-size: 30px; color: #1a3a5c; margin: 6px 0 20px;">
                <?php echo htmlspecialchars(CLUB_NAME); ?>
                <span style="color: #999; font-weight: normal;">vs</span>
                <?php echo htmlspecialchars($fixture['opposition']); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 25px;">

                <div>
                    <div style="font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 0.5px;">Date</div>
                    <div style="font-size: 17px; color: #1a3a5c; font-weight: 600;">
                        <?php echo date('l, j F Y', strtotime($fixture['match_date'])); ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 0.5px;">Kick-off</div>
                    <div style="font-size: 17px; color: #1a3a5c; font-weight: 600;">
                        <?php echo date('H:i', strtotime($fixture['kick_off_time'])); ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 0.5px;">Venue</div>
                    <div style="font-size: 17px; color: #1a3a5c; font-weight: 600;">
                        <?php echo htmlspecialchars($fixture['venue']); ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 0.5px;">Price</div>
                    <div style="font-size: 17px; color: #1a3a5c; font-weight: 600;">
                        £<?php echo number_format($fixture['ticket_price'], 2); ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 12px; color: #777; text-transform: uppercase; letter-spacing: 0.5px;">Availability</div>
                    <div style="font-size: 17px; font-weight: 600; color: <?php echo $is_sold_out ? '#d9534f' : '#28a745'; ?>;">
                        <?php
                            if ($is_sold_out) echo 'Sold out';
                            else echo (int) $tickets_left . ' tickets left';
                        ?>
                    </div>
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

            <?php if ($is_bookable): ?>
                <a href="book.php?fixture_id=<?php echo (int) $fixture['fixture_id']; ?>" class="btn">
                    Book tickets →
                </a>
            <?php else: ?>
                <button class="btn" disabled style="background: #ccc; cursor: not-allowed;">
                    <?php echo $is_sold_out ? 'Sold out' : ucfirst(str_replace('_', ' ', $fixture['status'])); ?>
                </button>
            <?php endif; ?>

        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
