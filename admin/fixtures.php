<?php
require_once __DIR__ . '/../config/database.php';
$page_title = 'Fixtures';
require_once __DIR__ . '/../includes/admin_header.php';

// Read flash message from the URL (used after redirects)
$flash_message = $_GET['msg'] ?? '';
$flash_type    = $_GET['type'] ?? 'success';

// Load all fixtures, newest first
$stmt = $pdo->query("SELECT *
                     FROM fixtures
                     ORDER BY match_date DESC, kick_off_time DESC");
$fixtures = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
    <h2 style="color: #1a3a5c; margin: 0;">Fixtures</h2>
    <a href="fixture_form.php" class="btn">+ Add new fixture</a>
</div>

<?php if ($flash_message): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?>">
        <?php echo htmlspecialchars($flash_message); ?>
    </div>
<?php endif; ?>

<?php if (empty($fixtures)): ?>

    <div class="card">
        <p>No fixtures have been created yet.</p>
        <p style="margin-top: 10px;">Click <strong>Add new fixture</strong> to create the first one.</p>
    </div>

<?php else: ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Opposition</th>
                <th>Competition</th>
                <th>Date</th>
                <th>Kick-off</th>
                <th>Price</th>
                <th style="min-width: 180px;">Capacity</th>
                <th>Tickets Available</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fixtures as $fixture):

                // ---- DYNAMIC STATUS CALCULATION ----
                // Priority order: cancelled > finished (past) > sold out > upcoming
                $today          = date('Y-m-d');
                $tickets_left   = $fixture['total_tickets'] - $fixture['tickets_sold'];
                $is_past        = $fixture['match_date'] < $today;
                $is_today       = $fixture['match_date'] === $today;
                $is_sold_out    = $tickets_left <= 0;

                if ($fixture['status'] === 'cancelled') {
                    $display_status = 'cancelled';
                } elseif ($is_past) {
                    $display_status = 'finished';
                } elseif ($is_sold_out) {
                    $display_status = 'sold_out';
                } elseif ($is_today) {
                    $display_status = 'today';
                } else {
                    $display_status = 'upcoming';
                }

                // Status badge colors
                $badge_bg = '#e2e3e5';
                $badge_color = '#383d41';
                switch ($display_status) {
                    case 'upcoming':
                        $badge_bg = '#d4edda'; $badge_color = '#155724'; break;
                    case 'today':
                        $badge_bg = '#cce5ff'; $badge_color = '#004085'; break;
                    case 'sold_out':
                        $badge_bg = '#fff3cd'; $badge_color = '#856404'; break;
                    case 'finished':
                        $badge_bg = '#e2e3e5'; $badge_color = '#383d41'; break;
                    case 'cancelled':
                        $badge_bg = '#f8d7da'; $badge_color = '#721c24'; break;
                }

                // ---- PROGRESS BAR DATA ----
                $sold_pct = $fixture['total_tickets'] > 0
                    ? round(($fixture['tickets_sold'] / $fixture['total_tickets']) * 100)
                    : 0;

                // Bar color shifts as capacity fills
                if ($sold_pct >= 90)      $bar_color = '#d9534f';   // almost full → red
                elseif ($sold_pct >= 60)  $bar_color = '#f0ad4e';   // filling   → orange
                else                       $bar_color = '#5cb85c';   // plenty    → green
            ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($fixture['opposition']); ?></strong></td>
                    <td><?php echo htmlspecialchars($fixture['competition']); ?></td>
                    <td><?php echo date('D, j M Y', strtotime($fixture['match_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($fixture['kick_off_time'])); ?></td>
                    <td>£<?php echo number_format($fixture['ticket_price'], 2); ?></td>

                    <!-- Capacity: progress bar + numbers -->
                    <td>
                        <div style="background: #e9ecef; border-radius: 10px; height: 10px; overflow: hidden;">
                            <div style="background: <?php echo $bar_color; ?>; width: <?php echo $sold_pct; ?>%; height: 100%; transition: width 0.3s;"></div>
                        </div>
                        <div style="font-size: 12px; color: #666; margin-top: 4px;">
                            <?php echo (int) $fixture['tickets_sold']; ?> / <?php echo (int) $fixture['total_tickets']; ?>
                            (<?php echo $sold_pct; ?>%)
                        </div>
                    </td>

                    <!-- Tickets available -->
                    <td>
                        <strong style="color: <?php echo $is_sold_out ? '#d9534f' : '#1a3a5c'; ?>;">
                            <?php echo (int) $tickets_left; ?>
                        </strong>
                        <div style="font-size: 11px; color: #999;">left</div>
                    </td>

                    <!-- Status badge -->
                    <td>
                        <span style="font-size: 12px; padding: 3px 8px; border-radius: 3px;
                            background: <?php echo $badge_bg; ?>;
                            color: <?php echo $badge_color; ?>;
                            text-transform: capitalize;
                        ">
                            <?php echo htmlspecialchars(str_replace('_', ' ', $display_status)); ?>
                        </span>
                    </td>

                    <td>
                        <a href="fixture_form.php?id=<?php echo (int) $fixture['fixture_id']; ?>" class="btn-small btn-edit">Edit</a>
                        <a href="fixture_delete.php?id=<?php echo (int) $fixture['fixture_id']; ?>" class="btn-small btn-delete"
                           onclick="return confirm('Are you sure you want to delete this fixture? Existing bookings will not be deleted but will lose their fixture reference.');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
