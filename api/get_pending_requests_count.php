<?php
// ไฟล์: api/get_pending_requests_count.php
include '../config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !in_array($_SESSION['user_level'], ['admin', 'super_admin'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$department_id = $_SESSION['department_id'];
$total_pending = 0;

// 1. นับคำขอลาป่วย/ลากิจ (leave_requests)
$sql = "SELECT COUNT(*) FROM leave_requests WHERE department_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $department_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $c1);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$total_pending += $c1;

// 2. นับคำขอลาล่วงหน้า (future_leave_requests)
$sql = "SELECT COUNT(*) FROM future_leave_requests WHERE department_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $department_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $c2);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$total_pending += $c2;

// 3. นับคำขอสลับเวร (swap_requests)
$sql = "SELECT COUNT(*) FROM swap_requests WHERE status = 'pending' AND (
            original_schedule_id IN (SELECT id FROM schedules WHERE department_id = ?) 
            OR target_schedule_id IN (SELECT id FROM schedules WHERE department_id = ?)
        )";
// *หมายเหตุ: การนับ swap อาจซับซ้อนขึ้นอยู่กับ logic แต่เบื้องต้นใช้วิธีเช็ค department ของ user ที่เกี่ยวข้อง
// เพื่อความง่ายและรวดเร็ว อาจนับจาก user ในแผนกแทน
$sql_swap = "SELECT COUNT(*) FROM swap_requests sr 
             JOIN users u ON sr.user_id = u.id 
             WHERE u.department_id = ? AND sr.status = 'pending'";
$stmt = mysqli_prepare($conn, $sql_swap);
mysqli_stmt_bind_param($stmt, "i", $department_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $c3);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$total_pending += $c3;

echo json_encode(['count' => $total_pending]);
mysqli_close($conn);
?>