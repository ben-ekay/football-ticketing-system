<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_guard.php';

$fixture_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($fixture_id <= 0) {
    header("Location: fixtures.php?msg=" . urlencode('Invalid fixture ID.') . "&type=error");
    exit;
}

// Check if the fixture has any bookings already
$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE fixture_id = :id");
$stmt->execute(['id' => $fixture_id]);
$booking_count = $stmt->fetchColumn();

if ($booking_count > 0) {
    header("Location: fixtures.php?msg=" . urlencode("Cannot delete: this fixture already has $booking_count booking(s). Cancel the fixture instead.") . "&type=error");
    exit;
}

// Safe to delete
$stmt = $pdo->prepare("DELETE FROM fixtures WHERE fixture_id = :id");
$stmt->execute(['id' => $fixture_id]);

header("Location: fixtures.php?msg=" . urlencode('Fixture deleted successfully.'));
exit;
?>
