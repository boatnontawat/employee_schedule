<?php
// ไฟล์: api/get_badge_count.php (ปรับปรุงให้รวม Badge Count ทั้งหมด)
$config_path = dirname(__DIR__) . '/config.php';

if (file_exists($config_path)) {
    include $config_path;
} else {
    include '../config.php';
}

if (!isset($_SESSION['user_id'])) {
    // คืนค่าเป็น 0 ถ้าไม่ได้ล็อกอิน (ป้องกัน Error)
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['total_count' => 0, 'swap_pending' => 0, 'unread_noti' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$total_count = 0;
$swap_pending_count = 0;
$unread_noti_count = 0;

// 1. นับคำขอสลับเวรที่รอ User นี้อนุมัติ (เป็น User B/Target)
$sql_swap = "SELECT COUNT(*) FROM swap_requests WHERE target_user_id = ? AND status = 'pending'";
$stmt_swap = mysqli_prepare($conn, $sql_swap);
if ($stmt_swap) {
    mysqli_stmt_bind_param($stmt_swap, "i", $user_id);
    mysqli_stmt_execute($stmt_swap);
    mysqli_stmt_bind_result($stmt_swap, $swap_pending_count);
    mysqli_stmt_fetch($stmt_swap);
    mysqli_stmt_close($stmt_swap);
}

// 2. นับการแจ้งเตือนทั่วไปที่ยังไม่ได้อ่าน
$sql_noti = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt_noti = mysqli_prepare($conn, $sql_noti);
if ($stmt_noti) {
    mysqli_stmt_bind_param($stmt_noti, "i", $user_id);
    mysqli_stmt_execute($stmt_noti);
    mysqli_stmt_bind_result($stmt_noti, $unread_noti_count);
    mysqli_stmt_fetch($stmt_noti);
    mysqli_stmt_close($stmt_noti);
}

$total_count = $swap_pending_count + $unread_noti_count;

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'total_count' => $total_count,
    'swap_pending' => $swap_pending_count,
    'unread_noti' => $unread_noti_count
]);

mysqli_close($conn);
?>