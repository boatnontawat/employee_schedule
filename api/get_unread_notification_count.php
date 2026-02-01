<?php
// ไฟล์: api/get_unread_notification_count.php
include '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

// นับจำนวน Notification ที่ยังไม่อ่าน (is_read = 0)
$sql = "SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$count = $row['total'] ?? 0;

echo json_encode(['count' => $count]);
mysqli_close($conn);
?>