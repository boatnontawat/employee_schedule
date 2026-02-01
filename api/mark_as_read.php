<?php
// ไฟล์: api/mark_as_read.php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$noti_id = filter_input(INPUT_POST, 'noti_id', FILTER_SANITIZE_NUMBER_INT);

if (!$noti_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

// Update is_read = 1 โดยเช็คว่าเป็น Notification ของ User นี้จริงหรือไม่
$update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
$upd_stmt = mysqli_prepare($conn, $update_sql);

if ($upd_stmt) {
    mysqli_stmt_bind_param($upd_stmt, "ii", $noti_id, $user_id);
    if (mysqli_stmt_execute($upd_stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'DB execution failed']);
    }
    mysqli_stmt_close($upd_stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'DB prepare failed']);
}

mysqli_close($conn);
?>