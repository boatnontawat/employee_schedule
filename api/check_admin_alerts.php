<?php
// ไฟล์: api/check_admin_alerts.php
include '../config.php';

header('Content-Type: application/json');

// แก้ไข: ให้ super_admin สามารถใช้งานได้ด้วย
if (!isLoggedIn() || !in_array($_SESSION['user_level'], ['admin', 'super_admin'])) {
    echo json_encode(['found' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. ดึงแจ้งเตือนล่าสุดที่ยังไม่ได้อ่าน (is_read = 0)
$sql = "SELECT id, message, type, created_at FROM notifications 
        WHERE user_id = ? AND is_read = 0 
        ORDER BY created_at ASC LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // 2. ถ้าเจอ ให้ทำเครื่องหมายว่าอ่านแล้วทันที (เพื่อไม่ให้ Alert เด้งซ้ำ)
    $update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $upd_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($upd_stmt, "i", $row['id']);
    mysqli_stmt_execute($upd_stmt);
    
    // 3. ส่งข้อมูลกลับไปให้หน้า Dashboard แสดงผล
    echo json_encode([
        'found' => true,
        'message' => $row['message'],
        'type' => $row['type'], // success, warning, danger, info
        'time' => date('H:i', strtotime($row['created_at']))
    ]);
} else {
    echo json_encode(['found' => false]);
}

mysqli_close($conn);
?>