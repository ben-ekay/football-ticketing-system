<?php
require_once __DIR__ . '/../config/database.php';
$page_title = 'Scan Tickets';
require_once __DIR__ . '/../includes/admin_header.php';

// Get today's quick stats
$today = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_today
    FROM tickets t
    JOIN bookings b ON t.booking_id = b.booking_id
    JOIN fixtures f ON b.fixture_id = f.fixture_id
    WHERE f.match_date = :today
");
$stmt->execute(['today' => $today]);
$total_today = (int) $stmt->fetchColumn();
?>

<div class="scan-container">

    <h2 style="color: #1a3a5c; margin-bottom: 8px;">🎫 Scan tickets at the gate</h2>
    <p style="color: #555; margin-bottom: 25px;">
        Point your device's camera at a supporter's QR code to validate entry.
    </p>

    <div id="qr-reader"></div>

    <div id="scan-status" class="scan-status idle">
        <div class="icon">📷</div>
        <div class="main-text">Ready to start</div>
        <div class="sub-text">Click Start scanner to activate the camera</div>
    </div>

    <div class="scan-controls">
        <button id="scan-start" class="btn">▶ Start scanner</button>
        <button id="scan-stop" class="btn btn-secondary" style="display: none;">⏸ Stop scanner</button>
    </div>

    <div class="scan-stats">
        <div class="scan-stat">
            <div class="stat-num"><?php echo $total_today; ?></div>
            <div class="stat-label">Tickets sold today</div>
        </div>
        <div class="scan-stat">
            <div class="stat-num" id="count-valid">0</div>
            <div class="stat-label">Valid scans</div>
        </div>
        <div class="scan-stat">
            <div class="stat-num" id="count-invalid">0</div>
            <div class="stat-label">Invalid scans</div>
        </div>
    </div>

    <div style="margin-top: 30px; padding: 15px; background: #fff8e1; border-radius: 4px; border-left: 4px solid #ffa726;">
        <strong>📱 Tips for the gate volunteer:</strong>
        <ul style="margin: 10px 0 0 20px; font-size: 14px;">
            <li>Use a phone or tablet — the back camera works best.</li>
            <li>Allow camera access when prompted by the browser.</li>
            <li>Hold the QR code about 15-20cm from the camera.</li>
            <li>You'll hear a high beep for valid, low beep for invalid.</li>
            <li>Wait until the screen turns green before letting the supporter through.</li>
        </ul>
    </div>

</div>

<!-- QR scanner library from CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="<?php echo SITE_URL; ?>/assets/js/scanner.js"></script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
