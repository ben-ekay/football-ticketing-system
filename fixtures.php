<?php
require_once __DIR__ . '/../config/database.php';
$page_title = 'Upcoming Fixtures';
require_once __DIR__ . '/../includes/header.php';

// Only show upcoming fixtures, dated today or later, ordered chronologically
$stmt = $pdo->prepare("SELECT *
                       FROM fixtures
                       WHERE status = 'upcoming'
                         AND match_date >= CURDATE()
                       ORDER BY match_date ASC, kick_off_time ASC");
$stmt->execute();
$fixtures = $stmt->fetchAll();
?>

<section class="section">
    <div class="container">

        <h2>Upcoming home fixtures</h2>
        <p style="margin-bottom: 25px; color: #555;">
            Book your ticket online and walk straight through the turnstile with a digital QR code on matchday.
        </p>

        <?php if (empty($fixtures)): ?>

            <div class="card">
                <p>No upcoming fixtures at the moment. Check back soon!</p>
            </div>

        <?php else: ?>

            <?php foreach ($fixtures as $fixture):
                $tickets_left = $fixture['total_tickets'] - $fixture['tickets_sold'];
                $is_sold_out  = $tickets_left <= 0;
            ?>

                <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">

                    <div style="flex: 1; min-width: 250px;">
                        <div style="color: #4ca3dd; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?php echo htmlspecialchars($fixture['competition']); ?>
                        </div>

                        <h3 style="font-size: 22px; color: #1a3a5c; margin: 4px 0 8px;">
                            <?php echo htmlspecialchars(CLUB_NAME); ?>
                            <span style="color: #999; font-weight: normal;">vs</span>
                            <?php echo htmlspecialchars($fixture['opposition']); ?>
                        </h3>

                        <div style="color: #555; font-size: 15px;">
                            📅 <?php echo date('l, j F Y', strtotime($fixture['match_date'])); ?>
                            &nbsp;&nbsp;
                            🕒 <?php echo date('H:i', strtotime($fixture['kick_off_time'])); ?>
                        </div>

                        <div style="color: #555; font-size: 15px; margin-top: 4px;">
                            📍 <?php echo htmlspecialchars($fixture['venue']); ?>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <div style="font-size: 24px; font-weight: 700; color: #1a3a5c;">
                            £<?php echo number_format($fixture['ticket_price'], 2); ?>
                        </div>
                        <div style="font-size: 13px; color: #777; margin-bottom: 10px;">
                            <?php if ($is_sold_out): ?>
                                <span style="color: #d9534f; font-weight: 600;">Sold out</span>
                            <?php else: ?>
                                <?php echo (int) $tickets_left; ?> tickets left
                            <?php endif; ?>
                        </div>

                        <?php if ($is_sold_out): ?>
                            <button class="btn" disabled style="background: #ccc; cursor: not-allowed;">Sold out</button>
                        <?php else: ?>
                            <a href="fixture_detail.php?id=<?php echo (int) $fixture['fixture_id']; ?>" class="btn">View &amp; book</a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
