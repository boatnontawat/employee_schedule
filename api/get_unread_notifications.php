<?php
// ไฟล์: api/get_unread_notifications.php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['found' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

// [แก้ไข] เปลี่ยน LIMIT 5 เป็น LIMIT 1 เพื่อให้เด้งทีละอัน (ไม่ซ้อนกัน)
$sql = "SELECT id, message, type, created_at FROM notifications 
        WHERE user_id = ? AND is_read = 0 
        ORDER BY created_at ASC LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = [
        'id' => $row['id'],
        'message' => $row['message'],
        'type' => $row['type'],
        'time' => date('H:i', strtotime($row['created_at']))
    ];
}

if (!empty($notifications)) {
    echo json_encode(['found' => true, 'data' => $notifications]);
} else {
    echo json_encode(['found' => false]);
}

mysqli_close($conn);
?>