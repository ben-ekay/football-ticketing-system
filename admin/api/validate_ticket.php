<?php
// ============================================
// API endpoint: validate a QR-scanned ticket
// ============================================
// Receives a qr_token via POST, validates it, marks it as used,
// and returns a JSON response.

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin_guard.php';

// Check it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'reason' => 'Method not allowed']);
    exit;
}

// Read the JSON body
$input = json_decode(file_get_contents('php://input'), true);
$qr_token = isset($input['qr_token']) ? trim($input['qr_token']) : '';

if ($qr_token === '') {
    echo json_encode([
        'ok'     => false,
        'status' => 'invalid',
        'reason' => 'No QR token received.'
    ]);
    exit;
}

// Validate format: tokens are 64 hex characters
if (!preg_match('/^[a-f0-9]{64}$/', $qr_token)) {
    echo json_encode([
        'ok'     => false,
        'status' => 'invalid',
        'reason' => 'This QR code is not a valid GoalTicket.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // Look up the ticket with all related info
    $stmt = $pdo->prepare("
        SELECT
            t.ticket_id, t.qr_token, t.status, t.scanned_at,
            b.booking_id,
            f.fixture_id, f.opposition, f.competition, f.match_date, f.kick_off_time,
            u.first_name, u.last_name
        FROM tickets t
        JOIN bookings b ON t.booking_id = b.booking_id
        JOIN fixtures f ON b.fixture_id = f.fixture_id
        JOIN users u    ON b.user_id    = u.user_id
        WHERE t.qr_token = :token
        FOR UPDATE
    ");
    $stmt->execute(['token' => $qr_token]);
    $ticket = $stmt->fetch();

    // Case 1: token not found in database
    if (!$ticket) {
        $pdo->rollBack();
        echo json_encode([
            'ok'     => false,
            'status' => 'invalid',
            'reason' => 'Ticket not found. This QR is not valid.'
        ]);
        exit;
    }

    // Case 2: ticket already used
    if ($ticket['status'] === 'used') {
        $pdo->rollBack();
        echo json_encode([
            'ok'         => false,
            'status'     => 'already_used',
            'reason'     => 'This ticket has already been used.',
            'scanned_at' => $ticket['scanned_at'],
            'holder'     => $ticket['first_name'] . ' ' . $ticket['last_name'],
            'opposition' => $ticket['opposition']
        ]);
        exit;
    }

    // Case 3: ticket is for a different day (allow same day only)
    $match_date = $ticket['match_date'];
    $today      = date('Y-m-d');
    if ($match_date !== $today) {
        $pdo->rollBack();
        echo json_encode([
            'ok'         => false,
            'status'     => 'wrong_day',
            'reason'     => "This ticket is for $match_date, not today.",
            'holder'     => $ticket['first_name'] . ' ' . $ticket['last_name'],
            'opposition' => $ticket['opposition'],
            'match_date' => $match_date
        ]);
        exit;
    }

    // Case 4: VALID! Mark as used
    $stmt = $pdo->prepare("
        UPDATE tickets
        SET status = 'used',
            scanned_at = NOW(),
            scanned_by = :admin_id
        WHERE ticket_id = :ticket_id
    ");
    $stmt->execute([
        'admin_id'  => $_SESSION['admin_id'],
        'ticket_id' => $ticket['ticket_id']
    ]);

    $pdo->commit();

    echo json_encode([
        'ok'         => true,
        'status'     => 'valid',
        'ticket_id'  => $ticket['ticket_id'],
        'holder'     => $ticket['first_name'] . ' ' . $ticket['last_name'],
        'opposition' => $ticket['opposition'],
        'competition' => $ticket['competition'],
        'kick_off'   => date('H:i', strtotime($ticket['kick_off_time']))
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'ok'     => false,
        'status' => 'error',
        'reason' => 'A server error occurred. Please try again.'
    ]);
}
?>
