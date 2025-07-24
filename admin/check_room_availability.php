<?php
require '../require/db.php';

$room_id = (int)($_POST['room_id'] ?? 0);
$check_in_date = $_POST['check_in_date'] ?? '';
$check_out_date = $_POST['check_out_date'] ?? '';
$exclude_booking_id = isset($_POST['exclude_booking_id']) ? (int)$_POST['exclude_booking_id'] : 0;

if (!$room_id || !$check_in_date || !$check_out_date) {
    echo json_encode(['available' => false, 'message' => 'Invalid data']);
    exit;
}

$where = "room_id = $room_id AND status = 1 AND (check_in_date < '$check_out_date' AND check_out_date > '$check_in_date')";
if ($exclude_booking_id) {
    $where .= " AND id != $exclude_booking_id";
}
$query = "SELECT COUNT(*) as cnt FROM bookings WHERE $where";
$result = $mysqli->query($query);
$overlap = $result ? $result->fetch_assoc()['cnt'] : 0;

if ($overlap > 0) {
    echo json_encode(['available' => false, 'message' => 'This room is already booked for the selected dates.']);
} else {
    echo json_encode(['available' => true]);
}
exit; 