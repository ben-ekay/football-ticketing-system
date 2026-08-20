<?php
// ============================================
// GoalTicket - Email helper
// ============================================
// In development, outgoing emails are saved as HTML files in /email_log/
// In production, this would be replaced with PHPMailer + a real SMTP server.

require_once __DIR__ . '/qr_helper.php';

/**
 * Send the booking confirmation "email" with embedded QR tickets.
 * In dev mode: saves the email as an HTML file in /email_log/.
 *
 * @param array $booking The booking + fixture + user details
 * @param array $tickets The tickets generated for this booking
 * @return bool          True if saved successfully
 */
function sendBookingEmail(array $booking, array $tickets): bool {

    $club_name = defined('CLUB_NAME') ? CLUB_NAME : 'the club';

    // Build the HTML body
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Your tickets for <?php echo htmlspecialchars($booking['opposition']); ?></title>
    </head>
    <body style="font-family: Arial, sans-serif; background: #f4f6f8; padding: 20px; margin: 0;">

        <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 6px; overflow: hidden;">

            <div style="background: #1a3a5c; color: #fff; padding: 25px; text-align: center;">
                <h1 style="margin: 0; font-size: 26px;">
                    Goal<span style="color: #4ca3dd;">Ticket</span>
                </h1>
                <p style="margin: 8px 0 0; font-size: 14px; opacity: 0.85;">Your booking is confirmed</p>
            </div>

            <div style="padding: 25px;">

                <p>Hi <?php echo htmlspecialchars($booking['first_name']); ?>,</p>

                <p>Thanks for booking with <?php echo htmlspecialchars($club_name); ?>. Your tickets for the match below are attached as QR codes.</p>

                <div style="background: #f4f6f8; padding: 15px; border-radius: 4px; margin: 20px 0;">
                    <div style="color: #4ca3dd; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?php echo htmlspecialchars($booking['competition']); ?>
                    </div>
                    <div style="font-size: 18px; font-weight: 700; color: #1a3a5c; margin-top: 4px;">
                        <?php echo htmlspecialchars($club_name); ?> vs <?php echo htmlspecialchars($booking['opposition']); ?>
                    </div>
                    <div style="font-size: 14px; color: #555; margin-top: 6px;">
                        📅 <?php echo date('l, j F Y', strtotime($booking['match_date'])); ?>
                        · 🕒 <?php echo date('H:i', strtotime($booking['kick_off_time'])); ?>
                        <br>📍 <?php echo htmlspecialchars($booking['venue']); ?>
                    </div>
                </div>

                <h3 style="color: #1a3a5c; margin-top: 25px;">Your QR tickets</h3>
                <p style="font-size: 14px; color: #555;">Show each QR code at the turnstile on matchday. Each code can only be used once.</p>

                <?php foreach ($tickets as $index => $ticket): ?>
                    <div style="text-align: center; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                        <div style="font-size: 14px; color: #777; margin-bottom: 8px;">
                            Ticket <?php echo ($index + 1); ?> of <?php echo count($tickets); ?>
                        </div>
                        <img src="<?php echo generateQrDataUri($ticket['qr_token']); ?>"
                             alt="QR code"
                             style="max-width: 220px; height: auto;">
                        <div style="font-size: 12px; color: #999; margin-top: 8px;">
                            Ticket ID: <?php echo (int) $ticket['ticket_id']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <p style="margin-top: 25px; font-size: 14px; color: #555;">
                    You can also view your tickets anytime in the
                    <a href="<?php echo SITE_URL; ?>/public/my_tickets.php" style="color: #4ca3dd;">My Tickets</a>
                    section of your account.
                </p>

                <p style="margin-top: 20px; font-size: 13px; color: #999;">
                    Booking reference: #<?php echo (int) $booking['booking_id']; ?>
                </p>

            </div>

            <div style="background: #1a3a5c; color: #cfd8e3; text-align: center; padding: 15px; font-size: 12px;">
                <?php echo htmlspecialchars($club_name); ?> · See you on matchday!
            </div>

        </div>

    </body>
    </html>
    <?php
    $html_body = ob_get_clean();

    // Save the email to a file (development mode)
    $email_log_dir = __DIR__ . '/../email_log';
    if (!is_dir($email_log_dir)) {
        mkdir($email_log_dir, 0755, true);
    }

    $timestamp = date('Y-m-d_His');
    $safe_email = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $booking['email']);
    $filename = $email_log_dir . "/{$timestamp}_{$safe_email}_booking{$booking['booking_id']}.html";

    return file_put_contents($filename, $html_body) !== false;
}
?>
