<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
$page_title = 'Help & Support';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
    <div class="container">

        <h2 style="text-align: center;">Help & Support</h2>
        <p style="text-align: center; max-width: 640px; margin: 0 auto 50px; color: #555;">
            Got a question about your tickets or booking? Find quick answers below, or get in touch directly — we're here to help.
        </p>

        <!-- Contact cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 50px;">

            <div class="card" style="text-align: center;">
                <div style="font-size: 42px; margin-bottom: 12px;">📞</div>
                <h3>Call us</h3>
                <p style="color: #555; margin: 10px 0;">Matchday: 9am – kick-off<br>Mon–Fri: 10am – 5pm</p>
                <a href="tel:+441912345678" style="color: var(--electric-cyan); font-weight: 700; text-decoration: none; font-size: 18px;">
                    +44 191 234 5678
                </a>
            </div>

            <div class="card" style="text-align: center;">
                <div style="font-size: 42px; margin-bottom: 12px;">✉️</div>
                <h3>Email us</h3>
                <p style="color: #555; margin: 10px 0;">We typically reply<br>within 24 hours.</p>
                <a href="mailto:support@goalticket.club" style="color: var(--electric-cyan); font-weight: 700; text-decoration: none;">
                    support@goalticket.club
                </a>
            </div>

            <div class="card" style="text-align: center;">
                <div style="font-size: 42px; margin-bottom: 12px;">📍</div>
                <h3>Visit us</h3>
                <p style="color: #555; margin: 10px 0;">
                    Sam Smith's Park<br>
                    Newcastle upon Tyne<br>
                    NE7 7EJ
                </p>
            </div>

        </div>

        <!-- FAQ -->
        <h2 style="margin-bottom: 30px;">Frequently asked questions</h2>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    How do I book a ticket?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    Browse the <a href="fixtures.php" style="color: var(--electric-cyan);">Fixtures page</a>, choose the match you'd like to attend, select how many tickets you need, and complete the booking. You'll receive your QR ticket immediately by email and in the My Tickets section of your account.
                </p>
            </details>
        </div>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    What do I do with the QR code on matchday?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    Open your ticket in the My Tickets section (or in the email confirmation) and show the QR code to a steward at the turnstile. They'll scan it with the GoalTicket scanner app and let you straight through. Each QR code can only be used once, so don't share it with anyone else.
                </p>
            </details>
        </div>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    Can I get a refund if I can't attend?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    Tickets are non-refundable once a match is within 24 hours of kick-off. For earlier cancellations, please contact us by email at <a href="mailto:support@goalticket.club" style="color: var(--electric-cyan);">support@goalticket.club</a> with your booking reference and we'll do our best to help.
                </p>
            </details>
        </div>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    My QR code isn't loading — what do I do?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    First, make sure you're signed in to your account. If the QR still doesn't appear, try refreshing the page or opening the confirmation email you received after booking — the QR is embedded directly inside. If neither works, give us a call and we'll resend your ticket in seconds.
                </p>
            </details>
        </div>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    Can I install GoalTicket as an app?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    Yes! On Android: open Chrome and tap the "Install" button when it appears (or use the menu → Install App). On iPhone: tap the Share button in Safari, then choose "Add to Home Screen". The app works exactly like the website but loads faster and is a tap away from your home screen.
                </p>
            </details>
        </div>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    Do you have seats with disabled access?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    Yes. Sam Smith's Park has dedicated accessible viewing areas and an accessible entrance (Entrance A). Please get in touch before matchday so we can reserve a suitable space and let our stewards know you're coming.
                </p>
            </details>
        </div>

        <div class="card" style="margin-bottom: 12px;">
            <details>
                <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
                    Is my payment information secure?
                </summary>
                <p style="margin-top: 12px; color: #555;">
                    Absolutely. GoalTicket uses encrypted connections (HTTPS) for all payment data, and we never store your card details on our servers. Your booking reference and QR token are unique and randomly generated, so they cannot be guessed or copied.
                </p>
            </details>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
