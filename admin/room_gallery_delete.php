<?php
require '../require/db.php';
require '../require/common_function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    $room_id = intval($_POST['room_id']);

    // Delete images from disk
    $result = selectData('room_galleries', $mysqli, '*', "WHERE room_id = $room_id");
    while ($row = $result->fetch_assoc()) {
        $file = '../' . $row['image_path'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Delete from database
    $delete = deleteData('room_galleries', $mysqli, "room_id = $room_id");

    if ($delete) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
