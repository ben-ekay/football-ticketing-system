<?php
require_once __DIR__ . '/../config/database.php';
$page_title = 'Help & Resources';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<h2 style="color: var(--stadium-dark); margin-bottom: 8px;">Help & Resources</h2>
<p style="color: #555; margin-bottom: 30px;">
    Quick references and contacts for managing GoalTicket on matchday and during the week.
</p>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px;">

    <div class="card" style="text-align: center;">
        <div style="font-size: 38px; margin-bottom: 10px;">🛠️</div>
        <h3>Technical Support</h3>
        <p style="color: #555; margin: 10px 0;">For app crashes, scanner not working, or login issues.</p>
        <a href="mailto:dev@goalticket.club" style="color: var(--electric-cyan); font-weight: 700; text-decoration: none;">
            dev@goalticket.club
        </a>
    </div>

    <div class="card" style="text-align: center;">
        <div style="font-size: 38px; margin-bottom: 10px;">📞</div>
        <h3>Matchday Helpline</h3>
        <p style="color: #555; margin: 10px 0;">Available from 9am to kick-off on match days.</p>
        <a href="tel:+441912345678" style="color: var(--electric-cyan); font-weight: 700; text-decoration: none; font-size: 18px;">
            +44 191 234 5678
        </a>
    </div>

    <div class="card" style="text-align: center;">
        <div style="font-size: 38px; margin-bottom: 10px;">📋</div>
        <h3>Operations Manager</h3>
        <p style="color: #555; margin: 10px 0;">For fixture changes, ticketing policy, club affairs.</p>
        <a href="mailto:ops@goalticket.club" style="color: var(--electric-cyan); font-weight: 700; text-decoration: none;">
            ops@goalticket.club
        </a>
    </div>

</div>

<h2 style="color: var(--stadium-dark); margin-bottom: 20px;">Staff Quick Reference</h2>

<div class="card" style="margin-bottom: 12px;">
    <details open>
        <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
            🎫 How to scan a supporter's ticket
        </summary>
        <ol style="margin: 12px 0 0 22px; color: #555; line-height: 1.7;">
            <li>From the Dashboard, tap <strong>Scan Tickets</strong> in the top menu.</li>
            <li>Tap <strong>▶ Start scanner</strong> and allow camera access when prompted.</li>
            <li>Hold the supporter's QR code about 15–20 cm from the camera.</li>
            <li>A high beep + green screen means valid. Let them through.</li>
            <li>A low beep + red screen means invalid — see the on-screen message for the reason.</li>
        </ol>
    </details>
</div>

<div class="card" style="margin-bottom: 12px;">
    <details>
        <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
            ⚠️ What if a ticket shows "Already used"?
        </summary>
        <p style="margin-top: 12px; color: #555;">
            This means the QR code has already been scanned earlier today (or on a previous matchday). The scanner shows the holder's name and the time it was originally scanned. If the supporter insists it wasn't them, please check ID and contact the Operations Manager before letting them through. Each QR code can only be used once by design — this prevents ticket-sharing and counterfeit entries.
        </p>
    </details>
</div>

<div class="card" style="margin-bottom: 12px;">
    <details>
        <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
            📅 Adding a new fixture
        </summary>
        <ol style="margin: 12px 0 0 22px; color: #555; line-height: 1.7;">
            <li>Tap <strong>Fixtures</strong> in the top menu.</li>
            <li>Tap <strong>+ Add new fixture</strong>.</li>
            <li>Fill in opposition, competition, date, kick-off, venue, price and total tickets.</li>
            <li>Save. The fixture appears immediately to supporters.</li>
            <li>Aim to publish home fixtures at least 14 days in advance.</li>
        </ol>
    </details>
</div>

<div class="card" style="margin-bottom: 12px;">
    <details>
        <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
            🚫 Cancelling or rescheduling a fixture
        </summary>
        <p style="margin-top: 12px; color: #555;">
            Edit the fixture and change its status to <strong>Cancelled</strong>. The system marks the fixture as cancelled on the public site, and the Operations Manager will need to email all affected supporters to arrange refunds or exchanges. There is no automatic refund flow yet — this is a planned feature.
        </p>
    </details>
</div>

<div class="card" style="margin-bottom: 12px;">
    <details>
        <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
            📱 Best devices for the scanner
        </summary>
        <p style="margin-top: 12px; color: #555;">
            Any phone or tablet with a back camera and a recent version of Chrome or Safari. iPad works very well and gives the steward a bigger screen. Make sure the device is fully charged at the start of the match and connected to the club's Wi-Fi if possible.
        </p>
    </details>
</div>

<div class="card" style="margin-bottom: 12px;">
    <details>
        <summary style="cursor: pointer; font-weight: 700; color: var(--stadium-dark); padding: 4px 0;">
            🔒 Security checklist
        </summary>
        <ul style="margin: 12px 0 0 22px; color: #555; line-height: 1.7;">
            <li>Never share your admin password with anyone.</li>
            <li>Log out of the scanner at the end of every matchday.</li>
            <li>Do not let supporters use any admin device themselves.</li>
            <li>If a device is lost or stolen, contact Technical Support immediately so the session can be invalidated.</li>
        </ul>
    </details>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
