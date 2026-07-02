<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_guard.php';

// Determine if we're editing (id present) or creating (no id)
$fixture_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$is_edit = $fixture_id > 0;

$errors = [];

// Default empty values
$opposition     = '';
$competition    = 'League';
$match_date     = '';
$kick_off_time  = '15:00';
$venue          = 'Home Ground';
$ticket_price   = '10.00';
$total_tickets  = '500';
$status         = 'upcoming';

// If editing, load the existing data
if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM fixtures WHERE fixture_id = :id");
    $stmt->execute(['id' => $fixture_id]);
    $fixture = $stmt->fetch();

    if (!$fixture) {
        header("Location: fixtures.php?msg=" . urlencode('Fixture not found.') . "&type=error");
        exit;
    }

    $opposition    = $fixture['opposition'];
    $competition   = $fixture['competition'];
    $match_date    = $fixture['match_date'];
    $kick_off_time = $fixture['kick_off_time'];
    $venue         = $fixture['venue'];
    $ticket_price  = $fixture['ticket_price'];
    $total_tickets = $fixture['total_tickets'];
    $status        = $fixture['status'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read input
    $opposition    = trim($_POST['opposition']    ?? '');
    $competition   = trim($_POST['competition']   ?? '');
    $match_date    = trim($_POST['match_date']    ?? '');
    $kick_off_time = trim($_POST['kick_off_time'] ?? '');
    $venue         = trim($_POST['venue']         ?? '');
    $ticket_price  = trim($_POST['ticket_price']  ?? '');
    $total_tickets = trim($_POST['total_tickets'] ?? '');
    $status        = trim($_POST['status']        ?? 'upcoming');

    // Validate
    if ($opposition === '')   $errors[] = 'Opposition team is required.';
    if ($competition === '')  $errors[] = 'Competition is required.';
    if ($match_date === '')   $errors[] = 'Match date is required.';
    if ($kick_off_time === '') $errors[] = 'Kick-off time is required.';
    if ($venue === '')        $errors[] = 'Venue is required.';

    if (!is_numeric($ticket_price) || $ticket_price < 0) {
        $errors[] = 'Ticket price must be a positive number.';
    }

    if (!is_numeric($total_tickets) || $total_tickets < 1) {
        $errors[] = 'Total tickets must be at least 1.';
    }

    // Save if valid
    if (empty($errors)) {

        if ($is_edit) {
            // UPDATE existing fixture
            $stmt = $pdo->prepare("UPDATE fixtures SET
                opposition = :opposition,
                competition = :competition,
                match_date = :match_date,
                kick_off_time = :kick_off_time,
                venue = :venue,
                ticket_price = :ticket_price,
                total_tickets = :total_tickets,
                status = :status
                WHERE fixture_id = :id");

            $stmt->execute([
                'opposition'    => $opposition,
                'competition'   => $competition,
                'match_date'    => $match_date,
                'kick_off_time' => $kick_off_time,
                'venue'         => $venue,
                'ticket_price'  => $ticket_price,
                'total_tickets' => $total_tickets,
                'status'        => $status,
                'id'            => $fixture_id
            ]);

            header("Location: fixtures.php?msg=" . urlencode('Fixture updated successfully.'));
            exit;

        } else {
            // INSERT new fixture
            $stmt = $pdo->prepare("INSERT INTO fixtures
                (opposition, competition, match_date, kick_off_time, venue, ticket_price, total_tickets, status)
                VALUES
                (:opposition, :competition, :match_date, :kick_off_time, :venue, :ticket_price, :total_tickets, :status)");

            $stmt->execute([
                'opposition'    => $opposition,
                'competition'   => $competition,
                'match_date'    => $match_date,
                'kick_off_time' => $kick_off_time,
                'venue'         => $venue,
                'ticket_price'  => $ticket_price,
                'total_tickets' => $total_tickets,
                'status'        => $status
            ]);

            header("Location: fixtures.php?msg=" . urlencode('New fixture created successfully.'));
            exit;
        }
    }
}

$page_title = $is_edit ? 'Edit Fixture' : 'New Fixture';
require_once __DIR__ . '/../includes/admin_header.php';
?>

<h2 style="color: #1a3a5c; margin-bottom: 20px;">
    <?php echo $is_edit ? 'Edit fixture' : 'Add new fixture'; ?>
</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Please fix the following:</strong>
        <ul style="margin: 8px 0 0 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo $is_edit ? 'fixture_form.php?id=' . $fixture_id : 'fixture_form.php'; ?>" method="POST" class="card" style="max-width: 700px;">

    <div class="form-group">
        <label for="opposition">Opposition team *</label>
        <input type="text" id="opposition" name="opposition"
               value="<?php echo htmlspecialchars($opposition); ?>"
               placeholder="e.g. Blyth Spartans" required>
    </div>

    <div class="form-group">
        <label for="competition">Competition *</label>
        <select id="competition" name="competition" required>
            <option value="League"           <?php if ($competition === 'League') echo 'selected'; ?>>League</option>
            <option value="FA Cup"           <?php if ($competition === 'FA Cup') echo 'selected'; ?>>FA Cup</option>
            <option value="FA Trophy"        <?php if ($competition === 'FA Trophy') echo 'selected'; ?>>FA Trophy</option>
            <option value="FA Vase"          <?php if ($competition === 'FA Vase') echo 'selected'; ?>>FA Vase</option>
            <option value="League Cup"       <?php if ($competition === 'League Cup') echo 'selected'; ?>>League Cup</option>
            <option value="Friendly"         <?php if ($competition === 'Friendly') echo 'selected'; ?>>Friendly</option>
            <option value="County Cup"       <?php if ($competition === 'County Cup') echo 'selected'; ?>>County Cup</option>
        </select>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="match_date">Match date *</label>
            <input type="date" id="match_date" name="match_date"
                   value="<?php echo htmlspecialchars($match_date); ?>" required>
        </div>

        <div class="form-group">
            <label for="kick_off_time">Kick-off time *</label>
            <input type="time" id="kick_off_time" name="kick_off_time"
                   value="<?php echo htmlspecialchars($kick_off_time); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label for="venue">Venue *</label>
        <input type="text" id="venue" name="venue"
               value="<?php echo htmlspecialchars($venue); ?>"
               placeholder="e.g. Sam Smith's Park" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="ticket_price">Ticket price (£) *</label>
            <input type="number" id="ticket_price" name="ticket_price"
                   step="0.50" min="0"
                   value="<?php echo htmlspecialchars($ticket_price); ?>" required>
        </div>

        <div class="form-group">
            <label for="total_tickets">Total tickets available *</label>
            <input type="number" id="total_tickets" name="total_tickets"
                   min="1"
                   value="<?php echo htmlspecialchars($total_tickets); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="upcoming"  <?php if ($status === 'upcoming')  echo 'selected'; ?>>Upcoming</option>
            <option value="sold_out"  <?php if ($status === 'sold_out')  echo 'selected'; ?>>Sold Out</option>
            <option value="cancelled" <?php if ($status === 'cancelled') echo 'selected'; ?>>Cancelled</option>
            <option value="finished"  <?php if ($status === 'finished')  echo 'selected'; ?>>Finished</option>
        </select>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 10px;">
        <button type="submit" class="btn"><?php echo $is_edit ? 'Save changes' : 'Create fixture'; ?></button>
        <a href="fixtures.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
